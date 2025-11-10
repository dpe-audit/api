<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Enveloppe\{WithApportRule, WithDeperditionRule, WithInertieRule};

final class PerformanceRefroidissementRule extends Rule
{
    use WithApportRule, WithDeperditionRule, WithInertieRule, WithBatimentRule;

    /**
     * Liste des consommations de refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get(
            'consommations',
            fn(): ConsommationCollection => $this->input()->refroidissement->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection)
        );
    }

    /**
     * Besoin de refroidissement en kWh
     */
    public function bfr(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['bfr', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois) => $this->bfr($scenario, $mois));
            }
            $text_fr = $this->text_fr($scenario, $mois);
            $nref_fr = $this->nref_fr($scenario, $mois);

            if (!$text_fr || !$nref_fr) {
                return 0;
            }
            if (0.5 > $this->rbth($scenario, $mois)) {
                return 0;
            }
            $fut = $this->fut($mois);
            $tint = $this->tint($scenario);

            $gv = $this->gv() / 1000;
            $bfr = $this->apport_fr($scenario, $mois) / 1000;
            $bfr -= $fut * $gv * ($tint - $text_fr) * $nref_fr;
            return max($bfr, 0);
        });
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth(Scenario $scenario, Mois $mois): float
    {
        return $this->get(self::implode(['rbth', $scenario, $mois]), function () use ($scenario, $mois): float {
            $gv = $this->gv();
            $apports = $this->apport_fr($scenario, $mois);
            $text_fr = $this->text_fr($scenario, $mois);
            $nref_fr = $this->nref_fr($scenario, $mois);
            $rbth = $gv * ($text_fr - $this->tint($scenario)) * $nref_fr;
            return $rbth ? $apports / $rbth : 0;
        });
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut(Mois $mois, Scenario $scenario = Scenario::CONVENTIONNEL): float
    {
        return $this->get(self::implode(['fut', $scenario, $mois]), function () use ($scenario, $mois): float {
            $t = $this->t();
            $rbth = $this->rbth($scenario, $mois);
            $a = 1 + ($t / 15);

            if ($rbth == 1) {
                return $a / ($a + 1);
            }
            if ($rbth > 0) {
                return (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1));
            }
            return 0;
        });
    }

    /**
     * Température de consigne en froid exprimée en °C
     */
    public function tint(Scenario $scenario = Scenario::CONVENTIONNEL): float
    {
        return $this->get(self::implode(['tint', $scenario]), fn() => match ($scenario) {
            Scenario::CONVENTIONNEL => 26,
            Scenario::DEPENSIER => 28,
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
            bfr: $this->bfr(Scenario::CONVENTIONNEL),
            consommations: $this->consommations(),
        ));
    }
}
