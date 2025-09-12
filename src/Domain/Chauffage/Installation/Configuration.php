<?php

namespace App\Domain\Chauffage\Installation;

use App\Domain\Chauffage\Systeme\Configuration as ConfigurationSysteme;

/**
 * @see https://github.com/dpe-audit/methode-3cl/discussions/25
 */
enum Configuration: string
{
    // Installation de chauffage simple avec ou sans appoint
    case BASE = 'base';
        // Installation de chauffage avec une PAC en relève d'une chaudière bois avec ou sans appoint
    case BASE_BOIS_RELEVE_PAC = 'base_bois_releve_pac';
        // Installation de chauffage avec une chaudière en relève d'une chaudière bois avec ou sans appoint
    case BASE_BOIS_RELEVE_CHAUDIERE = 'base_bois_releve_chaudiere';
        // Installation de chauffage avec chaudière en relève de PAC avec ou sans appoint
    case BASE_PAC_RELEVE_CHAUDIERE = 'base_pac_releve_chaudiere';
        // Installation de chauffage avec un ou plusieurs systèmes de chauffage central avec ou sans appoint
    case AUTRES = 'autres';
        // Installation de chauffage avec un ou plusieurs systèmes de chauffage divisé
    case DIVISE = 'divise';

    public function rdim(ConfigurationSysteme $configuration): float
    {
        return match ($configuration) {
            ConfigurationSysteme::BASE => $this->base(),
            ConfigurationSysteme::RELEVE => $this->releve(),
            ConfigurationSysteme::APPOINT => $this->appoint(),
        };
    }

    public function base(): float
    {
        return match ($this) {
            Configuration::BASE => 1,
            Configuration::BASE_BOIS_RELEVE_PAC => 0.75,
            Configuration::BASE_BOIS_RELEVE_CHAUDIERE => 0.75,
            Configuration::BASE_PAC_RELEVE_CHAUDIERE => 0.8,
            Configuration::AUTRES => 1,
        };
    }

    public function releve(): float
    {
        return match ($this) {
            Configuration::BASE => 0,
            Configuration::BASE_BOIS_RELEVE_PAC => 0.25,
            Configuration::BASE_BOIS_RELEVE_CHAUDIERE => 0.25,
            Configuration::BASE_PAC_RELEVE_CHAUDIERE => 0.2,
            Configuration::AUTRES => 0,
        };
    }

    public function appoint(): float
    {
        return 0.25;
    }
}
