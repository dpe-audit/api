<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Refroidissement\Generateur\Generateur;
use App\Engine\{Context, RuleIterator};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\RefroidissementTableValeurRepository;

/**
 * @extends RuleIterator<Generateur>
 */
final class PerformanceGenerateurRule extends RuleIterator
{
    use WithBatimentRule;

    public function __construct(
        private RefroidissementTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->refroidissement->generateurs()->values();
    }

    public function seer_saisi(): ?float
    {
        return $this->item()->seer();
    }

    public function annee_installation(): int
    {
        return current(array_filter([
            $this->item()->annee_installation(),
            $this->input()->batiment->annee_construction,
        ]));
    }

    /**
     * @return float[]
     */
    public function rdim_systemes(): array
    {
        return $this->input()->refroidissement->systemes()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->rdim())
            ->values();
    }

    public function bfr(): float
    {
        return $this->require(PerformanceRefroidissementRule::class)->bfr();
    }

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum($this->rdim_systemes());
        });
    }

    /**
     * Coefficient d'efficience énergétique
     */
    public function eer(): float
    {
        return $this->get('eer', function () {
            if ($this->seer_saisi()) {
                return $this->seer_saisi() * 0.95;
            }
            return $this->repository->eer(
                zone_climatique: $this->zone_climatique(),
                annee_installation_generateur: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire EER non trouvé');
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
                rdim: $this->rdim(),
                eer: $this->eer(),
            ));
        }
    }
}
