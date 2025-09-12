<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Engine\Input\Ecs\SystemeInputRuleIterator;
use App\Engine\Table\EcsTableValeurRepository;

abstract class RendementSystemeRule extends SystemeInputRuleIterator
{
    public function __construct(protected EcsTableValeurRepository $repository) {}

    /**
     * Inverse du rendement du système
     */
    public function iecs(): float
    {
        return $this->get('iecs', function (): float {
            return 1 / array_product([
                $this->rd(),
                $this->rg(),
                $this->rgs(),
                $this->rs(),
            ]);
        });
    }

    /**
     * Rendement annuel de distribution
     */
    final public function rd(): float
    {
        return $this->get("rd", function () {
            return $this->repository->rd(
                production_volume_habitable: $this->item()->position_volume_chauffe(),
                reseau_collectif: $this->item()->generateur()->generateur_collectif(),
                bouclage_reseau: $this->item()->bouclage_reseau(),
                alimentation_contigue: $this->item()->alimentation_contigue(),
            ) ?? throw new \RuntimeException('Valeur forfaitaire Rd non trouvée');
        });
    }

    /**
     * Rendement annuel de stockage - Ne s'applique qu'aux systèmes électriques
     */
    public function rs(): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération/stockage
     */
    public function rgs(): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération
     */
    public function rg(): float
    {
        return 1;
    }

    protected function becs(): float
    {
        return $this->data()->ecs->becs();
    }

    protected function pertes_stockage(): float
    {
        return $this->get('pertes_stockage', function (): float {
            $pertes = 0;
            $pertes += $this->item()->pertes_stockage();
            $pertes += $this->item()->generateur()->pertes_stockage();
            return $pertes;
        });
    }
}
