<?php

namespace App\Domain\Ecs\Installation;

use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $fecs,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $fecs = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($fecs, 0);
        Assert::nullOrLessThanEq($fecs, 1);

        return new self(
            rdim: $rdim,
            fecs: $fecs,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $fecs = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            fecs: $fecs ?? $this->fecs,
        );
    }
}
