<?php

namespace App\Engine\Rules\Ecs\Performance;

use App\Engine\Input\Ecs\GenerateurInput;

final class PerformanceGenerateurAutresRule extends PerformanceGenerateurRule
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (GenerateurInput $item): bool {
            return false === PerformanceGenerateurCombustionRule::match($item)
                && false === PerformancePacRule::match($item);
        });
    }
}
