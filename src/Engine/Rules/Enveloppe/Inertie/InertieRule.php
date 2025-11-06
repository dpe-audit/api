<?php

namespace App\Engine\Rules\Enveloppe\Inertie;

use App\Domain\Enveloppe\Inertie;
use App\Engine\{Context, Rule};

final class InertieRule extends Rule
{
    // * Données intermédiaires

    /**
     * @return array<int, array{surface: float, inertie: Inertie}>
     */
    public function inertie_niveaux(): array
    {
        return $this->input()->enveloppe->niveaux()
            ->map(fn($item) => [
                'surface' => $item->surface(),
                'inertie' => $this->requireIterator(InertieNiveauRule::class, $item)->inertie(),
            ])->values();
    }

    // * Données calculées

    /**
     * Etat d'inertie de l'enveloppe
     */
    public function inertie(): Inertie
    {
        return $this->get('inertie', function (): Inertie {
            $inerties = [];
            $niveaux = $this->inertie_niveaux();

            foreach (Inertie::cases() as $inertie) {
                $inerties[$inertie->value] = array_sum(array_map(
                    fn(array $niveau) => $niveau['inertie'] === $inertie ? $niveau['surface'] : 0,
                    $niveaux
                ));
            }
            $inerties = max($inerties);
            $inerties = array_map(fn($item) => Inertie::from($item), array_keys($inerties));

            if (count($inerties) === 1) {
                return current($inerties);
            }
            if (count($inerties) === 3 || count($inerties) === 4) {
                return Inertie::MOYENNE;
            }
            return in_array(Inertie::LEGERE, $inerties) ? Inertie::MOYENNE : Inertie::LOURDE;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);
        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
            inertie: $this->inertie(),
        ));
    }
}
