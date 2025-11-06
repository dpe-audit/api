<?php

namespace App\Engine\Rules\Enveloppe\ConfortEte;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Baie\TypeFermeture;
use App\Domain\Enveloppe\ConfortEte\{ConfortEte, Performance};
use App\Domain\Enveloppe\Inertie;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Enveloppe\Deperdition\DeperditionPlancherHautRule;
use App\Engine\Rules\Enveloppe\WithInertieRule;

final class ConfortEteRule extends Rule
{
    use WithInertieRule;

    // * Données d'entrée

    public function presence_brasseur_air(): bool
    {
        return $this->input()->enveloppe->presence_brasseurs_air() ?? false;
    }

    // * Données interémédiaires

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

            foreach ($this->input()->enveloppe->planchers_hauts() as $item) {
                if ($item->mitoyennete() !== Mitoyennete::EXTERIEUR) {
                    continue;
                }
                $rule = $this->requireIterator(DeperditionPlancherHautRule::class, $item);

                if ($rule->isolation()) {
                    $surface += $rule->sdep();
                }
                $total += $rule->sdep();
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
            foreach ($this->input()->enveloppe->baies() as $item) {
                if (false === in_array($item->position()->orientation(), [
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

    // * Données calculées

    /**
     * Inertie lourde
     */
    public function inertie_lourde(): bool
    {
        return $this->get('inertie_lourde', function (): bool {
            return in_array($this->inertie(), [Inertie::TRES_LOURDE, Inertie::LOURDE]);
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
                $surfaces[$orientation->value] = $this->input()->enveloppe->baies()
                    ->with_orientation($orientation)
                    ->surface();
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

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
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
