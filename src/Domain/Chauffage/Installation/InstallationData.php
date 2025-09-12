<?php

namespace App\Domain\Chauffage\Installation;

use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $fch,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $fch = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThan($fch, 0);
        Assert::nullOrLessThanEq($fch, 1);

        return new self(
            rdim: $rdim,
            fch: $fch,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $fch = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            fch: $fch ?? $this->fch,
        );
    }
}
