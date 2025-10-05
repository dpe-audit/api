<?php

namespace App\Engine\Rules\ConfortEte;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Baie\TypeFermeture;
use App\Domain\Enveloppe\ConfortEte\{ConfortEte, Performance};
use App\Domain\Enveloppe\Inertie;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Engine\Input\Enveloppe\BaieInput;
use App\Engine\Rule;

final class ConfortEteRule extends Rule
{
    /**
     * Inertie lourde
     */
    public function inertie_lourde(): bool
    {
        return $this->get('inertie_lourde', function (): bool {
            return in_array($this->data()->enveloppe->inertie(), [Inertie::TRES_LOURDE, Inertie::LOURDE]);
        });
    }

    /**
     * Isolation majoritaire des planchers hauts
     * 
     * TODO: intégrer le cas des appartements au RDC ou étages intermédiaires
     */
    public function isolation_plancher_haut(): bool
    {
        return $this->get('isolation_plancher_haut', function (): bool {
            $surface = 0;
            $total = 0;

            foreach ($this->data()->enveloppe->planchers_hauts as $item) {
                if ($item->mitoyennete()  !== Mitoyennete::EXTERIEUR) {
                    continue;
                }
                if ($item->isolation()) {
                    $surface += $item->sdep();
                }
                $total += $item->sdep();
            }
            return $surface ? $surface > $total / 2 : true;
        });
    }

    /**
     * Indicateur de présence de protections solaires
     */
    public function presence_protection_solaire(): bool
    {
        return $this->get('presence_protection_solaire', function (): bool {
            foreach ($this->data()->enveloppe->baies as $item) {
                if (false === in_array($item->orientation(), [
                    Orientation::EST,
                    Orientation::SUD,
                    Orientation::OUEST,
                ])) {
                    continue;
                }
                if ($item->presence_protection_solaire()) {
                    continue;
                }
                if ($item->type_fermeture() !== TypeFermeture::SANS_FERMETURE) {
                    continue;
                }
                return false;
            }
            return true;
        });
    }

    /**
     * Un logement est dit traversant si, pour chaque orientation, la surface des baies est inférieure à 75%
     * de la surface totale des baies.
     */
    public function logement_traversant(): bool
    {
        return $this->get('logement_traversant', function (): bool {
            $surfaces = [];

            foreach (Orientation::cases() as $orientation) {
                $collection = array_filter(
                    $this->data()->enveloppe->baies,
                    fn(BaieInput $item) => $item->orientation() === $orientation
                );
                $surfaces[$orientation->value] = array_reduce(
                    $collection,
                    fn(float $carry, BaieInput $item) => $carry += $item->surface(),
                    0
                );
            }
            if (0 == $total = array_sum($surfaces)) {
                return true;
            }
            foreach ($surfaces as $surface) {
                if ($surface / $total >= 0.75) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Présence de brasseurs d'air
     */
    public function presence_brasseur_air(): bool
    {
        return $this->data()->enveloppe->presence_brasseurs_air();
    }

    /**
     * Performance d'été
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            if (!$this->presence_protection_solaire() && !$this->isolation_plancher_haut()) {
                return Performance::INSUFFISANT;
            }
            $count = count(array_filter([
                $this->inertie_lourde(),
                $this->logement_traversant(),
                $this->presence_brasseur_air()
            ]));

            return $count >= 2 ? Performance::BON : Performance::MOYEN;
        });
    }

    public function calcule(): void
    {
        $this->ressource()->enveloppe()->calcule($this->ressource()->enveloppe()->data()->with(
            confort_ete: ConfortEte::create(
                inertie_lourde: $this->inertie_lourde(),
                isolation_plancher_haut: $this->isolation_plancher_haut(),
                presence_protection_solaire: $this->presence_protection_solaire(),
                logement_traversant: $this->logement_traversant(),
                presence_brasseur_air: $this->presence_brasseur_air(),
                performance: $this->performance(),
            )
        ));
    }
}
