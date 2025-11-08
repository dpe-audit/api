<?php

namespace App\Engine\Rules\Production;

use App\Domain\Common\Enum\Mois;
use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;
use App\Engine\{Context, RuleIterator};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\ProductionTableValeurRepository;

/**
 * @extends RuleIterator<PanneauPhotovoltaique>
 */
final class ProductionPhotovoltaiqueRule extends RuleIterator
{
    use WithBatimentRule;

    public final const RENDEMENT_MODULE = 0.17;
    public final const COEFFICIENT_PERTE = 0.86;

    public function __construct(
        private ProductionTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->production->panneaux_photovoltaiques()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function surface_capteurs(): float
    {
        return $this->item()->surface() ?? $this->modules() * 1.6;
    }

    public function modules(): int
    {
        return $this->item()->modules();
    }

    public function inclinaison(): float
    {
        return $this->item()->inclinaison();
    }

    public function orientation(): float
    {
        return $this->item()->orientation();
    }

    /**
     * Production photovoltaïque du panneau en kWh/an
     */
    public function ppv(?Mois $mois = null): float
    {
        $key = $mois ? "ppv::{$mois->value}" : 'ppv';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->ppv($item));
            }
            $s = $this->surface_capteurs();
            $ppv = $this->kpv() * $s * self::RENDEMENT_MODULE;
            $ppv *= $this->epv($mois) * self::COEFFICIENT_PERTE;
            return $ppv;
        });
    }

    /**
     * Coefficient de pondération prenant en compte l’altération par rapport à l'orientation optimale
     */
    public function kpv(): float
    {
        return $this->get('kpv', function (): float {
            return $this->repository->kpv(
                orientation: $this->orientation(),
                inclinaison: $this->inclinaison(),
            ) ?? throw new \DomainException("Valeur forfaitaire kpv non trouvée");
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
                kpv: $rule->kpv(),
                ppv: $rule->ppv(),
            ));
        }
    }
}
