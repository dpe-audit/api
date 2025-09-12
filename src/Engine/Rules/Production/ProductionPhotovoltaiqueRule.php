<?php

namespace App\Engine\Rules\Production;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Production\PanneauPhotovoltaiqueInputRuleIterator;
use App\Engine\Table\ProductionTableValeurRepository;

final class ProductionPhotovoltaiqueRule extends PanneauPhotovoltaiqueInputRuleIterator
{
    public final const RENDEMENT_MODULE = 0.17;
    public final const COEFFICIENT_PERTE = 0.86;

    public function __construct(
        private ProductionTableValeurRepository $repository
    ) {}

    /**
     * Production photovoltaïque du panneau exprimée en kWh/an
     */
    public function ppv(): float
    {
        return $this->get('ppv', function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->ppv_j($mois), 0);
        });
    }

    /**
     * Production photovoltaïque du panneau pour le mois j en kWh/an
     */
    public function ppv_j(Mois $mois): float
    {
        return $this->get("ppv::{$mois->value}", function () use ($mois): float {
            $s = $this->item()->surface_capteurs();
            $ppv = $this->kpv() * $s * self::RENDEMENT_MODULE;
            $ppv *= $this->data()->batiment->epv($mois) * self::COEFFICIENT_PERTE;
            return $this->round($ppv);
        });
    }

    /**
     * Coefficient de pondération prenant en compte l’altération par rapport à l'orientation optimale
     */
    public function kpv(): float
    {
        return $this->get('kpv', function (): float {
            return $this->repository->kpv(
                orientation: $this->item()->orientation(),
                inclinaison: $this->item()->inclinaison(),
            ) ?? throw new \DomainException("Valeur forfaitaire kpv non trouvée");
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            kpv: $this->kpv(),
            ppv: $this->ppv(),
        ));
    }
}
