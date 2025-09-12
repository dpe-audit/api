<?php

namespace App\Engine\Rules\Chauffage\Dimensionnement;

use App\Domain\Chauffage\Installation\Configuration as ConfigurationInstallation;
use App\Domain\Chauffage\Systeme\Configuration;
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\Input\Chauffage\SystemeInputRuleIterator;

final class DimensionnementSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Ratio de dimensionnement du système de chauffage
     */
    public function rdim(): float
    {
        $systeme_collectif = $this->item()->systeme_collectif();
        $configuration = $this->item()->configuration();
        $configuration_installation = $systeme_collectif
            ? $this->item()->installation()->configuration_collective()
            : $this->item()->installation()->configuration_individuelle();

        $systemes_base = $this->item()->installation()->systemes_base($systeme_collectif);
        $systemes_appoint = $this->item()->installation()->systemes_appoint($systeme_collectif);

        $pn = $this->item()->generateur()->pn_saisi();
        $pn_base = $this->item()->installation()->pn_base($systeme_collectif);
        $pn_appoint = $this->item()->installation()->pn_appoint($systeme_collectif);

        $rdim = $configuration_installation->rdim($configuration);
        $rdim *= $this->item()->installation()->rdim();

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
            $configuration_installation = $this->item()->systeme_collectif()
                ? $this->item()->installation()->configuration_collective()
                : $this->item()->installation()->configuration_individuelle();

            $type_systeme = $this->item()->type();
            $type_generateur = $this->item()->generateur()->type();
            $energie_generateur = $this->item()->generateur()->energie();

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
