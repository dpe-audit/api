<?php

namespace App\Engine\Rules\Enveloppe;

use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\Rules\Enveloppe\Apport\ApportEnveloppeRule;
use App\Engine\Rules\WithRule;

trait WithApportRule
{
    use WithRule;

    public function apport_rule(): ApportEnveloppeRule
    {
        return $this->require(ApportEnveloppeRule::class);
    }

    public function f(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->apport_rule()->f($scenario, $mois);
    }

    public function apport(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->apport_rule()->apport($scenario, $mois);
    }

    public function apport_fr(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->apport_rule()->apport_fr($scenario, $mois);
    }
}
