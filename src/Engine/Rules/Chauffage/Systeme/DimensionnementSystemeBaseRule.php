<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Configuration;
use App\Engine\Rules\Chauffage\DimensionnementSystemeRule;

final class DimensionnementSystemeBaseRule extends DimensionnementSystemeRule
{
    public function supports(): bool
    {
        return $this->configuration() === Configuration::BASE;
    }

    /**
     * @inheritdoc
     */
    public function rdim(): float
    {
        $configuration_installation = $this->configuration_installation();

        $systeme_collectif = $this->systeme_collectif();
        $systemes_base = $this->nombre_systemes_base($systeme_collectif);
        $systemes_appoint = $this->nombre_systemes_appoint($systeme_collectif);

        $pn = $this->pn_saisi();
        $pn_base = $this->pn_base($systeme_collectif);

        $rdim = parent::rdim();
        $rdim *= $configuration_installation->base();

        if ($systemes_base > 1) {
            $rdim = $pn && $pn_base ? $rdim * ($pn / $pn_base) : $rdim * (1 / $systemes_base);
        }
        return $systemes_appoint ? $rdim * (1 - $configuration_installation->appoint()) : $rdim;
    }
}
