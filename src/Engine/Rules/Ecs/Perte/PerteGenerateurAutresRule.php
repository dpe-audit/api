<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Engine\Input\Ecs\GenerateurInput;

final class PerteGenerateurAutresRule extends PerteGenerateurRule
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (GenerateurInput $item): bool {
            return PerteGenerateurCombustionRule::supports($item) === false;
        });
    }
}
