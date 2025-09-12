<?php

namespace App\Engine\Rules\Chauffage\Performance;

use App\Engine\Input\Chauffage\GenerateurInput;

final class PerformanceRadiateurGazRule extends PerformanceGenerateurRule
{
    /** @inheritDoc */
    public function rpn(): float
    {
        return $this->get("rpn", function (): float {
            $annee_installation = $this->item()->annee_installation();
            $pn = $this->item()->pn();

            return $this->item()->rpn_saisi() ?? match (true) {
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

    public static function supports(GenerateurInput $item): bool
    {
        return $item->type()->is_radiateur_gaz() && false === $item->generateur_multi_batiment();
    }
}
