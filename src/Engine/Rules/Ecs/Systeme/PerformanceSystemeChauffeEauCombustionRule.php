<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\Rules\Ecs\{PerformanceGenerateurRule, PerformanceSystemeRule};

final class PerformanceSystemeChauffeEauCombustionRule extends PerformanceSystemeRule
{
    public static function supports(Systeme $entity): bool
    {
        $xor = [];

        // Générateur inconnu or multi bâtiment
        $xor[] = null === $entity->generateur()->type()
            && false === $entity->generateur()->position()->generateur_multi_batiment;

        // Chaudère à combustion or multi bâtiment
        $xor[] = $entity->generateur()->type()?->is_chaudiere()
            && $entity->generateur()->energie()?->is_combustible()
            && false === $entity->generateur()->position()->generateur_multi_batiment;

        // Chauffe-eau à combustion or multi bâtiment
        $xor[] = $entity->generateur()->type()?->is_chauffe_eau()
            && $entity->generateur()->energie()?->is_combustible()
            && false === $entity->generateur()->position()->generateur_multi_batiment;

        return in_array(true, $xor, true);
    }

    // * Données d'entrée

    public function position_chauffe_eau(): PositionChauffeEau
    {
        return $this->item()->generateur()->position()->position_chauffe_eau;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
    }

    public function volume_stockage_integre(): float
    {
        return $this->item()->generateur()->signaletique()->volume_stockage ?? 0;
    }

    // * Données intermédiaires

    public function qp0(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->qp0();
    }

    public function rpn(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->rpn();
    }

    public function pveilleuse(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pveilleuse();
    }

    // * Données de sortie

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            if (false === $this->type_generateur()->is_chauffe_eau()) {
                return parent::rg();
            }
            if (0 === $this->volume_stockage_integre()) {
                return parent::rg();
            }
            $becs = $this->becs();
            $rpn = $this->rpn();
            $qp0 = $this->qp0();
            $pveilleuse = $this->pveilleuse();

            $rg = 1 / $rpn;
            $rg += 1790 * ($qp0 / $becs);
            $rg += 6970 * ($pveilleuse / $becs);
            return 1 / $rg;
        });
    }

    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return $this->get("rgs", function (): float {
            $becs = $this->becs();
            $pertes = $this->pertes_stockage();
            $rpn = $this->rpn();
            $qp0 = $this->qp0();
            $pveilleuse = $this->pveilleuse();

            if ($this->type_generateur()->is_chauffe_eau()) {
                if ($this->volume_stockage()) {
                    return parent::rgs();
                }
                $rgs = 1 / $rpn;
                $rgs += (8592 * $qp0 + $pertes) / $becs;
                $rgs += 6970 * ($pveilleuse / $becs);
                return 1 / $rgs;
            }
            $rgs = 1 / $rpn;
            $rgs += (1790 * $qp0 + $pertes) / $becs;
            $rgs += 6970 * (0.5 * $pveilleuse / $becs);
            return 1 / $rgs;
        });
    }
}
