<?php

namespace App\Domain\Chauffage;

use Webmozart\Assert\Assert;

final class ChauffageData
{
    public function __construct(
        public readonly ?float $bch,
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $bch = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::nullOrGreaterThanEq($bch, 0);
        Assert::nullOrGreaterThanEq($cef, 0);
        Assert::nullOrGreaterThanEq($cep, 0);
        Assert::nullOrGreaterThanEq($eges, 0);

        return new self(
            bch: $bch,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }

    public function with(
        ?float $bch = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        return self::create(
            bch: $bch ?? $this->bch,
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
        );
    }
}
