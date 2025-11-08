<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformanceRadiateurGazRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_radiateur_gaz() && false === $this->generateur_multi_batiment();
    }

    /** @inheritDoc */
    public function rpn(): float
    {
        return $this->get("rpn", function (): float {
            $annee_installation = $this->annee_installation();
            $pn = $this->pn();

            return $this->rpn_saisi() ?? match (true) {
                $annee_installation < 2006 => match (true) {
                    $pn < 5 => 0.7,
                    $pn >= 5 => 0.73,
                },
                $annee_installation >= 2006 => match (true) {
                    $pn < 5 => 0.8,
                    $pn >= 5 => 0.82,
                },
            };
        });
    }

    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function rpint(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function qp0(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function pveilleuse(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function tfonc30(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function tfonc100(): ?float
    {
        return null;
    }
}
