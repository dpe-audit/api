<?php

namespace App\Engine\Rules\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Configuration;
use App\Engine\Rules\Chauffage\DimensionnementSystemeRule;

final class DimensionnementSystemeAppointRule extends DimensionnementSystemeRule
{
    public function supports(): bool
    {
        return $this->configuration() === Configuration::APPOINT;
    }

    /**
     * @inheritdoc
     */
    public function rdim(): float
    {
        $systeme_collectif = $this->systeme_collectif();
        $systemes_appoint = $this->nombre_systemes_appoint($systeme_collectif);

        $pn = $this->pn_saisi();
        $pn_appoint = $this->pn_appoint($systeme_collectif);

        $rdim = parent::rdim();
        $rdim *= $this->configuration_installation()->appoint();

        return $pn && $pn_appoint ? $rdim * ($pn / $pn_appoint) : $rdim * (1 / $systemes_appoint);
    }
}
