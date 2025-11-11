<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Engine\{Context, Rule};

final class PerformanceVentilationRule extends Rule
{
    /**
     * Liste des consommations de ventilation
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get(
            'consommations',
            fn(): ConsommationCollection => $this->input()->ventilation->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection)
        );
    }

    /**
     * Déperdition thermique par renouvellement d'air due au système de ventilation par degré
     * d'écart entre l'intérieur et l'extérieur en W/K
     */
    public function hvent(): float
    {
        return $this->get('hvent', function () {
            return $this->input()->ventilation->installations()
                ->map(fn($item) => $this->requireIterator(PerformanceInstallationRule::class, $item)->hvent())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Débit volumique conventionnel à reprendre moyen en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function (): float {
            return $this->input()->ventilation->installations()
                ->map(function ($item) {
                    $rule = $this->requireIterator(PerformanceInstallationRule::class, $item);
                    return $rule->qvarep_conv() * $rule->rdim();
                })
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Débit volumique conventionnel à souffler moyen en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        return $this->get('qvasouf_conv', function (): float {
            return $this->input()->ventilation->installations()
                ->map(function ($item) {
                    $rule = $this->requireIterator(PerformanceInstallationRule::class, $item);
                    return $rule->qvasouf_conv() * $rule->rdim();
                })
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Somme des modules d'entrée d'air moyen en m3/(h.m²)
     */
    public function smea_conv(): float
    {
        return $this->get('smea_conv', function (): float {
            return $this->input()->ventilation->installations()
                ->map(function ($item) {
                    $rule = $this->requireIterator(PerformanceInstallationRule::class, $item);
                    return $rule->smea_conv() * $rule->rdim();
                })
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->ventilation->calcule($context->input()->ventilation->data()->with(
            consommations: $this->consommations(),
        ));
    }
}
