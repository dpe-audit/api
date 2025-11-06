<?php

namespace App\Engine\Rules\Enveloppe\Inertie;

use App\Domain\Enveloppe\Inertie;
use App\Domain\Enveloppe\Niveau\Niveau;
use App\Domain\Enveloppe\Paroi\Inertie as InertieParoi;
use App\Engine\{Context, RuleIterator};

/**
 * @extends RuleIterator<Niveau>
 */
final class InertieNiveauRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->niveaux()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function inertie_paroi_verticale(): InertieParoi
    {
        return $this->item()->inertie_paroi_verticale();
    }

    public function inertie_plancher_haut(): InertieParoi
    {
        return $this->item()->inertie_plancher_haut();
    }

    public function inertie_plancher_bas(): InertieParoi
    {
        return $this->item()->inertie_plancher_bas();
    }

    // * Données calculées

    /**
     * Etat d'inertie du niveau
     */
    public function inertie(): Inertie
    {
        return $this->get('inertie', function () {
            $inerties = array_filter([
                $this->inertie_paroi_verticale(),
                $this->inertie_plancher_haut(),
                $this->inertie_plancher_bas(),
            ], fn(InertieParoi $item) => $item === InertieParoi::LOURDE);

            return match (true) {
                count($inerties) === 0 => Inertie::LEGERE,
                count($inerties) === 1 => Inertie::MOYENNE,
                count($inerties) === 2 => Inertie::LOURDE,
                count($inerties) === 3 => Inertie::TRES_LOURDE,
            };
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                inertie: $rule->inertie()
            ));
        }
    }
}
