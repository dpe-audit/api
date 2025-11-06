<?php

namespace App\Engine\Rules\Enveloppe;

use App\Domain\Common\Enum\Mois;
use App\Engine\Rules\Enveloppe\Apport\ApportEnveloppeRule;
use App\Engine\Rules\WithRule;

trait WithApportRule
{
    use WithRule;

    public function apport_rule(): ApportEnveloppeRule
    {
        return $this->require(ApportEnveloppeRule::class);
    }

    public function f(Mois $mois): float
    {
        return $this->apport_rule()->f($mois);
    }

    public function apport(): float
    {
        return $this->apport_rule()->apport();
    }

    public function apport_fr(): float
    {
        return $this->apport_rule()->apport_fr();
    }

    public function apport_j(Mois $mois): float
    {
        return $this->apport_rule()->apport_j($mois);
    }

    public function apport_fr_j(Mois $mois): float
    {
        return $this->apport_rule()->apport_fr_j($mois);
    }
}
