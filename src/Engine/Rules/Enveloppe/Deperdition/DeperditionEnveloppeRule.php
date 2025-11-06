<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Deperditions\{Deperditions, Performance};
use App\Engine\{Context, Rule};

final class DeperditionEnveloppeRule extends Rule
{
    // * Données intermdédiaires

    public function dp(): float
    {
        return $this->get('dp', function (): float {
            return $this->input()->enveloppe->parois()
                ->map(fn($item) => $this->requireIterator(DeperditionParoiRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dp_murs(): float
    {
        return $this->get('dp_murs', function (): float {
            return $this->input()->enveloppe->murs()
                ->map(fn($item) => $this->requireIterator(DeperditionMurRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dp_planchers_bas(): float
    {
        return $this->get('dp_planchers_bas', function (): float {
            return $this->input()->enveloppe->planchers_bas()
                ->map(fn($item) => $this->requireIterator(DeperditionPlancherBasRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dp_planchers_hauts(): float
    {
        return $this->get('dp_planchers_hauts', function (): float {
            return $this->input()->enveloppe->planchers_hauts()
                ->map(fn($item) => $this->requireIterator(DeperditionPlancherHautRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dp_baies(): float
    {
        return $this->get('dp_baies', function (): float {
            return $this->input()->enveloppe->baies()
                ->map(fn($item) => $this->requireIterator(DeperditionBaieRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dp_portes(): float
    {
        return $this->get('dp_portes', function (): float {
            return $this->input()->enveloppe->portes()
                ->map(fn($item) => $this->requireIterator(DeperditionPorteRule::class, $item)->dp())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function pt(): float
    {
        return $this->get('pt', function (): float {
            return $this->input()->enveloppe->ponts_thermiques()
                ->map(fn($item) => $this->requireIterator(DeperditionPontThermiqueRule::class, $item)->pt())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return $this->input()->enveloppe->parois()
                ->map(fn($item) => $this->requireIterator(DeperditionParoiRule::class, $item)->sdep())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function dr(): float
    {
        return $this->get('dr', function (): float {
            return $this->require(DeperditionRenouvellementAirRule::class)->dr();
        });
    }

    // * Données calculées

    /**
     * Déperditions thermiques en W/K
     */
    public function gv(): float
    {
        return $this->get('gv', function (): float {
            return $this->dp() + $this->pt() + $this->dr();
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

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
            deperditions: Deperditions::create(
                gv: $this->gv(),
                dp: $this->dp(),
                dp_murs: $this->dp_murs(),
                dp_planchers_bas: $this->dp_planchers_bas(),
                dp_planchers_hauts: $this->dp_planchers_hauts(),
                dp_baies: $this->dp_baies(),
                dp_portes: $this->dp_portes(),
                pt: $this->pt(),
                dr: $this->dr(),
                ubat: $this->ubat(),
                performance: $this->performance(),
            )
        ));
    }
}
