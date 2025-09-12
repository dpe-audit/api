<?php

namespace App\Domain\Refroidissement\Systeme;

use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
    ) {}

    public static function create(
        ?float $rdim = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        return new self(rdim: $rdim);
    }

    public function with(
        ?float $rdim = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
        );
    }
}
