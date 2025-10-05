<?php

namespace App\Domain\Refroidissement\Systeme;

use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $cef_fr,
        public readonly ?float $cep_fr,
        public readonly ?float $eges_fr,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $cef_fr = null,
        ?float $cep_fr = null,
        ?float $eges_fr = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($cef_fr, 0);
        Assert::nullOrGreaterThanEq($cep_fr, 0);
        Assert::nullOrGreaterThanEq($eges_fr, 0);
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);

        return new self(
            rdim: $rdim,
            cef_fr: $cef_fr,
            cep_fr: $cep_fr,
            eges_fr: $eges_fr,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $cef_fr = null,
        ?float $cep_fr = null,
        ?float $eges_fr = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            cef_fr: $cef_fr ?? $this->cef_fr,
            cep_fr: $cep_fr ?? $this->cep_fr,
            eges_fr: $eges_fr ?? $this->eges_fr,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
        );
    }
}
