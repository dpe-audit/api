<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Deperditions\{Deperditions, Performance};
use App\Engine\Input\Enveloppe\{ParoiInput, PontThermiqueInput};
use App\Engine\Rule;

final class DeperditionEnveloppeRule extends Rule
{
    /**
     * Déperditions thermiques en W/K
     */
    public function gv(): float
    {
        return $this->get('gv', function (): float {
            return $this->dp() + $this->pt() + $this->data()->enveloppe->dr();
        });
    }

    /**
     * Déperditions thermiques par les parois en W/K
     */
    public function dp(): float
    {
        return $this->get('dp', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->parois(),
            ));
        });
    }

    /**
     * Déperditions thermiques par les murs en W/K
     */
    public function dp_murs(): float
    {
        return $this->get('dp_murs', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->murs,
            ));
        });
    }

    /**
     * Déperditions thermiques par les planchers bas en W/K
     */
    public function dp_planchers_bas(): float
    {
        return $this->get('dp_planchers_bas', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->planchers_bas,
            ));
        });
    }

    /**
     * Déperditions thermiques par les planchers hauts en W/K
     */
    public function dp_planchers_hauts(): float
    {
        return $this->get('dp_planchers_hauts', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->planchers_hauts,
            ));
        });
    }

    /**
     * Déperditions thermiques par les baies en W/K
     */
    public function dp_baies(): float
    {
        return $this->get('dp_baies', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->baies,
            ));
        });
    }

    /**
     * Déperditions thermiques par les portes en W/K
     */
    public function dp_portes(): float
    {
        return $this->get('dp_portes', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->dp(),
                $this->data()->enveloppe->portes,
            ));
        });
    }

    /**
     * Déperditions thermiques par les ponts thermiques en W/K
     */
    public function pt(): float
    {
        return $this->get('pt', function (): float {
            return array_sum(array_map(
                fn(PontThermiqueInput $item) => $item->pt(),
                $this->data()->enveloppe->ponts_thermiques,
            ));
        });
    }

    /**
     * Somme des surfaces déperditives de l'enveloppe en m²
     */
    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return array_sum(array_map(
                fn(ParoiInput $item) => $item->sdep(),
                $this->data()->enveloppe->parois(),
            ));
        });
    }

    /**
     * Coefficient de transmission thermique de l'enveloppe exprimé en W/K.m²
     */
    public function ubat(): float
    {
        return $this->get('ubat', function (): float {
            return $this->gv() / $this->sdep();
        });
    }

    /**
     * Indicateur de performance de l'enveloppe
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_data($this->ubat());
        });
    }

    public function calcule(): void
    {
        $this->ressource()->enveloppe()->calcule($this->ressource()->enveloppe()->data()->with(
            deperditions: Deperditions::create(
                gv: $this->gv(),
                dp: $this->dp(),
                dp_murs: $this->dp_murs(),
                dp_planchers_bas: $this->dp_planchers_bas(),
                dp_planchers_hauts: $this->dp_planchers_hauts(),
                dp_baies: $this->dp_baies(),
                dp_portes: $this->dp_portes(),
                pt: $this->pt(),
                dr: $this->data()->enveloppe->dr(),
                ubat: $this->ubat(),
                performance: $this->performance(),
            )
        ));
    }
}
