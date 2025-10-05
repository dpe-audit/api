<?php

namespace App\Domain\Refroidissement\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $eer,
        public readonly ?float $rdim,
        public readonly ?float $cef_fr,
        public readonly ?float $cep_fr,
        public readonly ?float $eges_fr,
    ) {}

    public static function create(
        ?float $eer = null,
        ?float $rdim = null,
        ?float $cef_fr = null,
        ?float $cep_fr = null,
        ?float $eges_fr = null,
    ): self {
        Assert::nullOrGreaterThan($eer, 0);
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($cef_fr, 0);
        Assert::nullOrGreaterThanEq($cep_fr, 0);
        Assert::nullOrGreaterThanEq($eges_fr, 0);

        return new self(
            eer: $eer,
            rdim: $rdim,
            cef_fr: $cef_fr,
            cep_fr: $cep_fr,
            eges_fr: $eges_fr,
        );
    }

    public function with(
        ?float $eer = null,
        ?float $rdim = null,
        ?float $cef_fr = null,
        ?float $cep_fr = null,
        ?float $eges_fr = null,
    ): self {
        return self::create(
            eer: $eer ?? $this->eer,
            rdim: $rdim ?? $this->rdim,
            cef_fr: $cef_fr ?? $this->cef_fr,
            cep_fr: $cep_fr ?? $this->cep_fr,
            eges_fr: $eges_fr ?? $this->eges_fr,
        );
    }
}
