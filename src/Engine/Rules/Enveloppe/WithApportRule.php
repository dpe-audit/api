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

    public function f(?Mois $mois = null): float
    {
        return $this->apport_rule()->f($mois);
    }

    public function apport(?Mois $mois = null): float
    {
        return $this->apport_rule()->apport($mois);
    }

    public function apport_fr(?Mois $mois = null): float
    {
        return $this->apport_rule()->apport_fr($mois);
    }
}
