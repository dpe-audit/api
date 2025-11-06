<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Installation\Configuration as ConfigurationInstallation;
use App\Domain\Chauffage\Systeme\{Systeme, Configuration};
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Systeme>
 */
abstract class DimensionnementSystemeRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_systeme(): TypeChauffage
    {
        return $this->item()->type();
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->generateur()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->generateur()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function systeme_collectif(): bool
    {
        return $this->item()->generateur()->position()->generateur_collectif;
    }

    public function pn_saisi(): ?float
    {
        return $this->item()->generateur()->signaletique()->pn;
    }

    public function nombre_systemes_base(bool $systeme_collectif): int
    {
        return $this->item()->installation()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->map(fn($entity) => $this->requireIterator(static::class, $entity))
            ->filter(fn(PerformanceSystemeRule $rule) => $rule->configuration() === Configuration::BASE)
            ->count();
    }

    public function nombre_systemes_releve(bool $systeme_collectif): int
    {
        return $this->item()->installation()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->map(fn($entity) => $this->requireIterator(static::class, $entity))
            ->filter(fn(PerformanceSystemeRule $rule) => $rule->configuration() === Configuration::RELEVE)
            ->count();
    }

    public function nombre_systemes_appoint(bool $systeme_collectif): int
    {
        return $this->item()->installation()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->map(fn($entity) => $this->requireIterator(static::class, $entity))
            ->filter(fn(PerformanceSystemeRule $rule) => $rule->configuration() === Configuration::APPOINT)
            ->count();
    }

    public function pn_configuration(Configuration $configuration, bool $systeme_collectif): float
    {
        $pn = 0;
        foreach ($this->item()->installation()->systemes() as $item) {
            $rule = $this->requireIterator(static::class, $item);
            if ($rule->configuration() !== $configuration) {
                continue;
            }
            if ($rule->systeme_collectif() !== $systeme_collectif) {
                continue;
            }
            if (null === $rule->pn_saisi()) {
                return 0;
            }
            $pn += $rule->pn_saisi();
        }
        return $pn;
    }

    public function pn_base(bool $systeme_collectif): float
    {
        return $this->pn_configuration(Configuration::BASE, $systeme_collectif);
    }

    public function pn_releve(bool $systeme_collectif): float
    {
        return $this->pn_configuration(Configuration::RELEVE, $systeme_collectif);
    }

    public function pn_appoint(bool $systeme_collectif): float
    {
        return $this->pn_configuration(Configuration::APPOINT, $systeme_collectif);
    }

    // * Données intermédiaires

    public function configuration_installation(): ConfigurationInstallation
    {
        $rule = $this->requireIterator(DimensionnementInstallationRule::class, $this->item()->installation());
        return $this->systeme_collectif() ? $rule->configuration_collective() : $rule->configuration_individuelle();
    }

    public function rdim_installation(): float
    {
        return $this->requireIterator(DimensionnementInstallationRule::class, $this->item()->installation())->rdim();
    }

    // * Données de sortie

    /**
     * Ratio de dimensionnement du système de chauffage
     */
    public function rdim(): float
    {
        $configuration_installation = $this->configuration_installation();
        $configuration = $this->configuration();

        $systeme_collectif = $this->systeme_collectif();
        $systemes_base = $this->nombre_systemes_base($systeme_collectif);
        $systemes_appoint = $this->nombre_systemes_appoint($systeme_collectif);

        $pn = $this->pn_saisi();
        $pn_base = $this->pn_base($systeme_collectif);
        $pn_appoint = $this->pn_appoint($systeme_collectif);

        $rdim = $configuration_installation->rdim($configuration);
        $rdim *= $this->rdim_installation();

        if ($configuration === Configuration::BASE) {
            if ($systemes_base > 1) {
                return $pn_base ? $rdim * ($pn / $pn_base) : $rdim * (1 / $systemes_base);
            }
            return $systemes_appoint
                ? $rdim * (1 - $configuration_installation->appoint())
                : $rdim;
        }
        if ($configuration === Configuration::RELEVE) {
            return $systemes_appoint
                ? $rdim * (1 - $configuration_installation->appoint())
                : $rdim;
        }
        if ($configuration === Configuration::APPOINT) {
            return ($pn_appoint) ? $rdim * ($pn / $pn_appoint) : $rdim * (1 / $systemes_appoint);
        }
        throw new \DomainException('Configuration du système de chauffage inconnue');
    }

    /**
     * Configuration du système de chauffage
     * 
     * @see https://github.com/dpe-audit/methode-3cl/discussions/25
     */
    public function configuration(): Configuration
    {
        return $this->get('configuration', function (): Configuration {
            $configuration_installation = $this->configuration_installation();
            $type_systeme = $this->type_systeme();
            $type_generateur = $this->type_generateur();
            $energie_generateur = $this->energie_generateur();

            // Installation de chauffage avec un ou plusieurs systèmes de chauffage divisé
            if ($configuration_installation === ConfigurationInstallation::DIVISE) {
                return Configuration::BASE;
            }
            // Installation de chauffage simple avec ou sans appoint
            if ($configuration_installation === ConfigurationInstallation::BASE) {
                return $type_systeme === TypeChauffage::CHAUFFAGE_CENTRAL ? Configuration::BASE : Configuration::APPOINT;
            }
            // Installation de chauffage avec une PAC en relève d’une chaudière bois avec ou sans appoint
            if ($configuration_installation === ConfigurationInstallation::BASE_BOIS_RELEVE_PAC) {
                if ($type_generateur->is_pac()) {
                    return Configuration::RELEVE;
                }
                if ($type_generateur->is_chaudiere()) {
                    return Configuration::BASE;
                }
                if ($type_systeme === TypeChauffage::CHAUFFAGE_DIVISE) {
                    return Configuration::APPOINT;
                }
            }
            // Installation de chauffage avec une chaudière en relève d’une chaudière bois avec ou sans appoint
            if ($configuration_installation === ConfigurationInstallation::BASE_BOIS_RELEVE_CHAUDIERE) {
                if ($type_generateur->is_chaudiere()) {
                    return $energie_generateur->is_bois() ? Configuration::BASE : Configuration::RELEVE;
                }
                if ($type_systeme === TypeChauffage::CHAUFFAGE_DIVISE) {
                    return Configuration::APPOINT;
                }
            }
            // Installation de chauffage avec chaudière en relève de PAC avec ou sans appoint
            if ($configuration_installation === ConfigurationInstallation::BASE_PAC_RELEVE_CHAUDIERE) {
                if ($type_generateur->is_pac()) {
                    return Configuration::BASE;
                }
                if ($type_generateur->is_chaudiere()) {
                    return Configuration::RELEVE;
                }
                if ($type_systeme === TypeChauffage::CHAUFFAGE_DIVISE) {
                    return Configuration::APPOINT;
                }
            }
            // Installation de chauffage avec un ou plusieurs systèmes de chauffage central avec ou sans appoint
            if ($configuration_installation === ConfigurationInstallation::AUTRES) {
                return $type_systeme === TypeChauffage::CHAUFFAGE_CENTRAL ? Configuration::BASE : Configuration::APPOINT;
            }
            throw new \DomainException('Configuration du système indéterminée');
        });
    }
}
