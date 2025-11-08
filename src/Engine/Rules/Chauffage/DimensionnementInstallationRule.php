<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Installation\Configuration;

abstract class DimensionnementInstallationRule extends CommonInstallationRule
{
    /**
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->surface() / $this->surface_totale();
        });
    }

    /**
     * Configuration de chauffage pour une installation individuelle
     */
    public function configuration_individuelle(): ?Configuration
    {
        return $this->get('configuration_individuelle', function (): ?Configuration {
            return $this->installation_individuelle() ? $this->configuration(false) : null;
        });
    }

    /**
     * Configuration de chauffage pour une installation collective
     */
    public function configuration_collective(): ?Configuration
    {
        return $this->get('configuration_collective', function (): ?Configuration {
            return $this->installation_collective() ? $this->configuration(true) : null;
        });
    }

    public function configuration(bool $installation_collective): Configuration
    {
        $systemes_chauffage_central = $this->systemes_chauffage_central($installation_collective);
        $presence_chaudiere_bois = $this->presence_chaudiere_bois($installation_collective);
        $presence_chaudiere = $this->presence_chaudiere($installation_collective);
        $presence_pac = $this->presence_pac($installation_collective);

        // Installation de chauffage avec un ou plusieurs systèmes de chauffage divisé
        if ($systemes_chauffage_central === 0) {
            return Configuration::DIVISE;
        }
        // Installation de chauffage simple avec ou sans appoint
        if ($systemes_chauffage_central === 1) {
            return Configuration::BASE;
        }
        // Installation de chauffage avec un ou plusieurs systèmes de chauffage central avec ou sans appoint
        if ($systemes_chauffage_central > 2) {
            return Configuration::AUTRES;
        }
        // Installation de chauffage avec une PAC en relève d’une chaudière bois avec ou sans appoint
        if ($presence_chaudiere_bois && $presence_pac) {
            return Configuration::BASE_BOIS_RELEVE_PAC;
        }
        // Installation de chauffage avec une chaudière en relève d’une chaudière bois avec ou sans appoint
        if ($presence_chaudiere_bois && $presence_chaudiere) {
            return Configuration::BASE_BOIS_RELEVE_CHAUDIERE;
        }
        // Installation de chauffage avec chaudière en relève de PAC avec ou sans appoint
        if ($presence_chaudiere && $presence_pac) {
            return Configuration::BASE_PAC_RELEVE_CHAUDIERE;
        }
        // Configuration par défaut
        return Configuration::AUTRES;
    }
}
