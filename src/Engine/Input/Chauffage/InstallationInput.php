<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Installation\{Configuration, Installation};
use App\Domain\Chauffage\Installation\Regulation\Regulation;
use App\Domain\Chauffage\Systeme\Configuration as ConfigurationSysteme;
use App\Domain\Chauffage\TypeChauffage;
use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\ConsommationInstallationRule;
use App\Engine\Rules\Chauffage\Dimensionnement\DimensionnementInstallationRule;
use App\Engine\Rules\Chauffage\Rendement\RendementInstallationRule;
use App\Engine\Rules\Chauffage\Perte\PerteInstallationRule;

final class InstallationInput extends Input
{
    private ?array $systemes = null;
    private ?array $generateurs = null;

    public function __construct(
        public readonly Engine $context,
        public readonly Installation $entity,
    ) {}

    /**
     * @return SystemeInput[]
     */
    public function systemes(): array
    {
        return $this->systemes ??= array_filter(
            $this->context->data()->ecs->systemes,
            fn(SystemeInput $item) => $item->entity->installation() === $this->entity
        );
    }

    /**
     * @return GenerateurInput[]
     */
    public function generateurs(): array
    {
        return $this->generateurs ??= array_map(
            fn(SystemeInput $item) => $item->generateur(),
            $this->systemes()
        );
    }

    public function surface(): float
    {
        return $this->entity->surface();
    }

    public function surface_totale(): float
    {
        return $this->entity->chauffage()->installations()->surface();
    }

    public function comptage_individuel(): bool
    {
        return $this->entity->comptage_individuel();
    }

    public function installation_collective(): bool
    {
        return $this->entity->systemes()->has_generateur_collectif();
    }

    public function installation_individuelle(): bool
    {
        return $this->entity->systemes()->has_generateur_collectif();
    }

    public function solaire_thermique(): bool
    {
        return $this->entity->solaire_thermique() !== null;
    }

    public function fch_saisi(): ?float
    {
        return $this->entity->solaire_thermique()?->fch;
    }

    public function annee_installation_solaire_thermique(): ?int
    {
        if (null === $this->entity->solaire_thermique()) {
            return null;
        }
        return $this->entity->solaire_thermique()->annee_installation
            ?? $this->context->data()->batiment->annee_construction();
    }

    public function systemes_base(bool $systeme_collectif): int
    {
        return count(array_filter($this->systemes(), function (SystemeInput $item) use ($systeme_collectif) {
            return $item->systeme_collectif() === $systeme_collectif
                && $item->configuration() === ConfigurationSysteme::BASE;
        }));
    }

    public function systemes_releve(bool $systeme_collectif): int
    {
        return count(array_filter($this->systemes(), function (SystemeInput $item) use ($systeme_collectif) {
            return $item->systeme_collectif() === $systeme_collectif
                && $item->configuration() === ConfigurationSysteme::RELEVE;
        }));
    }

    public function systemes_appoint(bool $systeme_collectif): int
    {
        return count(array_filter($this->systemes(), function (SystemeInput $item) use ($systeme_collectif) {
            return $item->systeme_collectif() === $systeme_collectif
                && $item->configuration() === ConfigurationSysteme::APPOINT;
        }));
    }

    public function systemes_chauffage_central(bool $systeme_collectif): int
    {
        return $this->entity->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            ->count();
    }

    public function systemes_chauffage_divise(bool $systeme_collectif): int
    {
        return $this->entity->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->count();
    }

    public function presence_pac(bool $systeme_collectif): bool
    {
        return $this->entity->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_pac();
    }

    public function presence_chaudiere_bois(bool $systeme_collectif): bool
    {
        return $this->entity->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_chaudiere_bois();
    }

    public function presence_chaudiere(bool $systeme_collectif): bool
    {
        return $this->entity->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_chaudiere();
    }

    public function pn_cascade(bool $priorite): float
    {
        $pn = 0;
        foreach ($this->systemes() as $item) {
            if (false === $item->generateur()->energie()->is_combustible()) {
                continue;
            }
            if ($priorite && $item->generateur()->priorite_cascade() === null) {
                continue;
            }
            if (false === $priorite && $item->generateur()->priorite_cascade() !== null) {
                continue;
            }
            $pn += $item->generateur()->pn();
        }
        return $pn;
    }

    public function pn(ConfigurationSysteme $configuration, bool $systeme_collectif): float
    {
        $pn = 0;
        foreach ($this->systemes() as $item) {
            if ($item->configuration() !== $configuration) {
                continue;
            }
            if ($item->systeme_collectif() !== $systeme_collectif) {
                continue;
            }
            if (null === $item->generateur()->pn_saisi()) {
                return 0;
            }
            $pn += $item->generateur()->pn_saisi();
        }
        return $pn;
    }

    public function pn_base(bool $systeme_collectif): float
    {
        return $this->pn(ConfigurationSysteme::BASE, $systeme_collectif);
    }

    public function pn_releve(bool $systeme_collectif): float
    {
        return $this->pn(ConfigurationSysteme::RELEVE, $systeme_collectif);
    }

    public function pn_appoint(bool $systeme_collectif): float
    {
        return $this->pn(ConfigurationSysteme::APPOINT, $systeme_collectif);
    }

    public function regulation_centrale(): Regulation
    {
        return $this->entity->regulation_centrale();
    }

    public function regulation_terminale(): Regulation
    {
        return $this->entity->regulation_terminale();
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementInstallationRule
    {
        return $this->requireIterator(DimensionnementInstallationRule::class, $this);
    }

    public function rendement_rule(): RendementInstallationRule
    {
        return $this->requireIterator(RendementInstallationRule::class, $this);
    }

    public function configuration_individuelle(): ?Configuration
    {
        return $this->dimensionnement_rule()->configuration_individuelle();
    }

    public function configuration_collective(): ?Configuration
    {
        return $this->dimensionnement_rule()->configuration_collective();
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function fch(): float
    {
        return $this->rendement_rule()->fch();
    }

    public function pertes(): PerteCollection
    {
        /** @var PerteInstallationRule $rule */
        $rule = $this->requireIterator(PerteInstallationRule::class, $this);
        return $rule->pertes();
    }

    public function consommations(): ConsommationCollection
    {
        /** @var ConsommationInstallationRule $rule */
        $rule = $this->requireIterator(ConsommationInstallationRule::class, $this);
        return $rule->consommations();
    }
}
