<?php

namespace App\Engine\Rules\Ecs\Performance;

use App\Engine\Input\Ecs\GenerateurInput;

final class PerformancePacRule extends PerformanceGenerateurRule
{
    /**
     * @inheritDoc
     */
    public function cop(): float
    {
        return $this->get("cop", function (): float {
            if ($this->item()->cop_saisi()) {
                return $this->item()->cop_saisi();
            }
            return $this->repository->cop(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                type_generateur: $this->item()->type(),
                annee_installation: $this->item()->annee_installation(),
            ) ?? throw new \DomainException("Valeurs forfaitaires COP non trouvées");
        });
    }

    public static function match(GenerateurInput $item): bool
    {
        return $item->type()->is_pac() && false === $item->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), [static::class, 'match']);
    }
}
