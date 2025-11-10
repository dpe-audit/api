<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Refroidissement\Installation\Installation;
use App\Engine\{Context, RuleIterator};
use App\Engine\Rules\Batiment\WithBatimentRule;

/**
 * @extends RuleIterator<Installation>
 */
final class PerformanceInstallationRule extends RuleIterator
{
    use WithBatimentRule;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->refroidissement->installations()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function surface(): float
    {
        return $this->item()->surface();
    }

    // * Données de sortie

    /**
     * Liste des consommations de l'installation refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get(
            'consommations',
            fn(): ConsommationCollection => $this->item()->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection)
        );
    }

    /**
     * Ratio de dimensionnement de l'installation de refroidissement
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return min($this->surface() / $this->surface_reference(), 1);
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                consommations: $rule->consommations(),
            ));
        }
    }
}
