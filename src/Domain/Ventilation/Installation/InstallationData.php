<?php

namespace App\Domain\Ventilation\Installation;

use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $hvent,
        public readonly ?float $qvarep_conv,
        public readonly ?float $qvasouf_conv,
        public readonly ?float $smea_conv,
    ) {}

    public static function create(
        ?float $hvent = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
    ): self {
        Assert::nullOrGreaterThanEq($hvent, 0);
        Assert::nullOrGreaterThanEq($qvarep_conv, 0);
        Assert::nullOrGreaterThanEq($qvasouf_conv, 0);
        Assert::nullOrGreaterThanEq($smea_conv, 0);

        return new self(
            hvent: $hvent,
            qvarep_conv: $qvarep_conv,
            qvasouf_conv: $qvasouf_conv,
            smea_conv: $smea_conv,
        );
    }

    public function with(
        ?float $hvent = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
    ): self {
        return new self(
            hvent: $hvent ?? $this->hvent,
            qvarep_conv: $qvarep_conv ?? $this->qvarep_conv,
            qvasouf_conv: $qvasouf_conv ?? $this->qvasouf_conv,
            smea_conv: $smea_conv ?? $this->smea_conv,
        );
    }
}
