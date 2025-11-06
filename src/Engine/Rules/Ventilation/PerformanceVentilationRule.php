<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\{Context, Rule};

final class PerformanceVentilationRule extends Rule
{
    /**
     * Consommation d'énergie final des auxiliaires de ventilation en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->input()->ventilation->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->cef_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de ventilation en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->input()->ventilation->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->cep_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de ventilation en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->input()->ventilation->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->eges_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Déperdition thermique par renouvellement d'air due au système de ventilation par degré
     * d'écart entre l'intérieur et l'extérieur exprimées en W/K
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
            cef_aux: $this->cef_aux(),
            cep_aux: $this->cep_aux(),
            eges_aux: $this->eges_aux(),
        ));
    }
}
