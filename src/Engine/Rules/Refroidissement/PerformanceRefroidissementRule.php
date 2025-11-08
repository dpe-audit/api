<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Enveloppe\{WithApportRule, WithDeperditionRule, WithInertieRule};

final class PerformanceRefroidissementRule extends Rule
{
    use WithApportRule, WithDeperditionRule, WithInertieRule, WithBatimentRule;

    /**
     * Consommation d'énergie final de refroidissement en kWh/an
     */
    public function cef_fr(): float
    {
        return $this->get('cef_fr', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cef_fr())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire de refroidissement en kWh/an
     */
    public function cep_fr(): float
    {
        return $this->get('cep_fr', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cep_fr())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire de refroidissement en kWh/an
     */
    public function eges_fr(): float
    {
        return $this->get('eges_fr', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->eges_fr())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires de refroidissement en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cef_aux())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de refroidissement en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cep_aux())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de refroidissement en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->eges_aux())
                ->reduce(fn(float $carry, float $item) => $carry + $item);
        });
    }

    /**
     * Besoin de refroidissement en kWh
     */
    public function bfr(?Mois $mois = null): float
    {
        $key = $mois ? "bfr::{$mois->value}" : 'bfr';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->bfr($item));
            }
            $text_fr = $this->text_fr($mois);
            $nref_fr = $this->nref_fr($mois);

            if (!$text_fr || !$nref_fr) {
                return 0;
            }
            if (0.5 > $this->rbth($mois)) {
                return 0;
            }
            $fut = $this->fut($mois);
            $tint = $this->tint();

            $gv = $this->gv() / 1000;
            $bfr = $this->apport_fr($mois) / 1000;
            $bfr -= $fut * $gv * ($tint - $text_fr) * $nref_fr;
            return max($bfr, 0);
        });
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth(Mois $mois): float
    {
        return $this->get("rbth::{$mois->value}", function () use ($mois) {
            $gv = $this->gv();
            $apports = $this->apport_fr($mois);
            $text_fr = $this->text_fr($mois);
            $nref_fr = $this->nref_fr($mois);
            $rbth = $gv * ($text_fr - $this->tint()) * $nref_fr;
            return $rbth ? $apports / $rbth : 0;
        });
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut(Mois $mois): float
    {
        return $this->get("fut::{$mois->value}", function () use ($mois) {
            $t = $this->t();
            $rbth = $this->rbth($mois);
            $a = 1 + ($t / 15);

            return $rbth > 0 && $rbth !== 1
                ?  (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1))
                : $a / ($a + 1);
        });
    }

    /**
     * Température de consigne en froid exprimée en °C
     */
    public function tint(): float
    {
        return $this->get('tint', fn() => match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => 26,
            ScenarioUsage::DEPENSIER => 28,
        });
    }

    /**
     * Constante de temps de la zone pour le refroidissement exprimée en J/K
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            return $this->cin() / (3600 * $this->gv());
        });
    }

    /**
     * Capacité thermique intérieure efficace de la zone exprimée en J/K
     */
    public function cin(): float
    {
        return $this->get('cin', function (): float {
            return $this->inertie()->cin() * $this->surface_reference();
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->refroidissement->calcule($context->input()->refroidissement->data()->with(
            bfr: $this->bfr(),
            cef_fr: $this->cef_fr(),
            cep_fr: $this->cep_fr(),
            eges_fr: $this->eges_fr(),
            cef_aux: $this->cef_aux(),
            cep_aux: $this->cep_aux(),
            eges_aux: $this->eges_aux(),
        ));
    }
}
