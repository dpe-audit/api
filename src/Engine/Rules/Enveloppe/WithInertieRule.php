<?php

namespace App\Engine\Rules\Enveloppe;

use App\Domain\Enveloppe\Inertie;
use App\Engine\Rules\Enveloppe\Inertie\InertieRule;
use App\Engine\Rules\WithRule;

trait WithInertieRule
{
    use WithRule;

    public function inertie(): Inertie
    {
        /** @var InertieRule $rule */
        $rule = $this->require(InertieRule::class);
        return $rule->inertie();
    }
}
