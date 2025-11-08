<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurAutresRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->generateur_multi_batiment()
            || (false === $this->type()->is_chaudiere() && false === $this->type()->is_pac());
    }
}
