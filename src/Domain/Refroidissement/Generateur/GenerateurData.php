<?php

namespace App\Domain\Refroidissement\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $eer,
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $eer = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::nullOrGreaterThan($eer, 0);
        Assert::nullOrGreaterThan($cef, 0);
        Assert::nullOrGreaterThan($cep, 0);
        Assert::nullOrGreaterThan($eges, 0);
        return new self(eer: $eer, cef: $cef, cep: $cep, eges: $eges);
    }

    public function with(
        ?float $eer = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        return self::create(
            eer: $eer ?? $this->eer,
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
        );
    }
}
