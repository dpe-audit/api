<?php

namespace App\Domain\Ventilation;

use Webmozart\Assert\Assert;

final class VentilationData
{
    public function __construct(
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
    ) {}

    public static function create(
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);
        return new self(
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
        );
    }

    public function with(
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        return self::create(
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
        );
    }
}
