<?php

namespace App\Engine\Rules\Enveloppe;

use App\Engine\Rules\Enveloppe\Deperdition\DeperditionEnveloppeRule;
use App\Engine\Rules\WithRule;

trait WithDeperditionRule
{
    use WithRule;

    public function deperdition_rule(): DeperditionEnveloppeRule
    {
        return $this->require(DeperditionEnveloppeRule::class);
    }

    public function gv(): float
    {
        return $this->deperdition_rule()->gv();
    }

    public function sdep(): float
    {
        return $this->deperdition_rule()->sdep();
    }

    public function dp(): float
    {
        return $this->deperdition_rule()->dp();
    }
}
