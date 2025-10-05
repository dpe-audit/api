<?php

namespace App\Engine\Rules\Ecs\Dimensionnement;

use App\Engine\Input\Ecs\{GenerateurInputRuleIterator, SystemeInput};
use App\Engine\Table\EcsTableValeurRepository;

final class DimensionnementGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        private EcsTableValeurRepository $repository,
    ) {}

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Puissance nominale conventionnelle exprimée en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function () {
            return $this->item()->pn_saisi() ?? $this->item()->pecs();
        });
    }

    /**
     * Puissance de dimensionnement du générateur exprimée en kW
     */
    public function pdim(): float
    {
        return $this->get('pdim', function (): float {
            return ($pch = $this->item()->generateur_mixte()?->pch())
                ? max($pch, $this->pecs())
                : $this->pecs();
        });
    }

    /**
     * Puissance de dimensionnement du besoin d'eau chaude sanitaire en kW
     */
    public function pecs(): float
    {
        return $this->get('pecs', function (): float {
            if ($this->item()->pn_saisi()) {
                return $this->item()->pn_saisi();
            }
            $volume_stockage = $this->volume_stockage();
            return match (true) {
                $volume_stockage === 0 => 21,
                $volume_stockage <= 20 => 21 - 0.8 * $volume_stockage,
                $volume_stockage <= 150 => 5 - 1.751 * (($volume_stockage - 20) / 65),
                $volume_stockage > 150 => (7.14 * $volume_stockage + 428) / 1000,
            };
        });
    }

    /**
     * Volume de stockage
     */
    public function volume_stockage(): float
    {
        return $this->get('volume_stockage', function (): float {
            return $this->item()->volume_stockage() + array_sum(array_map(
                fn(SystemeInput $item) => $item->volume_stockage(),
                $this->item()->systemes(),
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            rdim: $this->rdim(),
            pn: $this->pn(),
            pdim: $this->pdim(),
            pecs: $this->pecs(),
        ));
    }
}
