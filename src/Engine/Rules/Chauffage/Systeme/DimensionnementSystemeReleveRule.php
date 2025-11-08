<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Configuration;
use App\Engine\Rules\Chauffage\DimensionnementSystemeRule;

final class DimensionnementSystemeReleveRule extends DimensionnementSystemeRule
{
    public function supports(): bool
    {
        return $this->configuration() === Configuration::RELEVE;
    }

    /**
     * @inheritdoc
     */
    public function rdim(): float
    {
        $configuration_installation = $this->configuration_installation();

        $systeme_collectif = $this->systeme_collectif();
        $systemes_releve = $this->nombre_systemes_releve($systeme_collectif);
        $systemes_appoint = $this->nombre_systemes_appoint($systeme_collectif);

        $pn = $this->pn_saisi();
        $pn_releve = $this->pn_releve($systeme_collectif);

        $rdim = parent::rdim();
        $rdim *= $configuration_installation->releve();

        if ($systemes_releve > 1) {
            $rdim = $pn && $pn_releve ? $rdim * ($pn / $pn_releve) : $rdim * (1 / $systemes_releve);
        }
        return $systemes_appoint ? $rdim * (1 - $configuration_installation->appoint()) : $rdim;
    }
}
