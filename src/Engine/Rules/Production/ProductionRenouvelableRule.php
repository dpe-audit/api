<?php

namespace App\Engine\Rules\Production;

use App\Engine\Input\Production\PanneauPhotovoltaiqueInput;
use App\Engine\Rule;

final class ProductionRenouvelableRule extends Rule
{
    /**
     * Production photovoltaïque en kWh/an
     */
    public function ppv(): float
    {
        return $this->get('ppv', function (): float {
            return array_reduce(
                $this->data()->production->panneaux_photovoltaiques,
                fn(float $carry, PanneauPhotovoltaiqueInput $item) => $carry += $item->ppv(),
                0,
            );
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->production()->calcule($this->ressource()->production()->data()->with(
            ppv: $this->ppv(),
        ));
    }
}
