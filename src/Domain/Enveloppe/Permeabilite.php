<?php

namespace App\Domain\Enveloppe\Permeabilite;

use Webmozart\Assert\Assert;

final class Permeabilite
{
    public function __construct(
        public readonly ?float $hvent,
        public readonly ?float $hperm,
        public readonly ?float $q4pa_conv,
        public readonly ?float $qvarep_conv,
        public readonly ?float $qvasouf_conv,
        public readonly ?float $smea_conv,
    ) {}

    public static function create(
        ?float $hvent = null,
        ?float $hperm = null,
        ?float $q4pa_conv = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
    ): self {
        Assert::nullOrGreaterThanEq($hvent, 0);
        Assert::nullOrGreaterThanEq($hperm, 0);
        Assert::nullOrGreaterThanEq($q4pa_conv, 0);
        Assert::nullOrGreaterThanEq($qvarep_conv, 0);
        Assert::nullOrGreaterThanEq($qvasouf_conv, 0);
        Assert::nullOrGreaterThanEq($smea_conv, 0);

        return new self(
            hvent: $hvent,
            hperm: $hperm,
            q4pa_conv: $q4pa_conv,
            qvarep_conv: $qvarep_conv,
            qvasouf_conv: $qvasouf_conv,
            smea_conv: $smea_conv,
        );
    }

    public function with(
        ?float $hvent = null,
        ?float $hperm = null,
        ?float $q4pa_conv = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
    ): self {
        return static::create(
            hvent: $hvent ?? $this->hvent,
            hperm: $hperm ?? $this->hperm,
            q4pa_conv: $q4pa_conv ?? $this->q4pa_conv,
            qvarep_conv: $qvarep_conv ?? $this->qvarep_conv,
            qvasouf_conv: $qvasouf_conv ?? $this->qvasouf_conv,
            smea_conv: $smea_conv ?? $this->smea_conv,
        );
    }
}
