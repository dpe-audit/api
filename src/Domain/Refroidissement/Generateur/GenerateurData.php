<?php

namespace App\Domain\Refroidissement\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $eer,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $eer = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThan($eer, 0);

        return new self(
            rdim: $rdim,
            eer: $eer,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $eer = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            eer: $eer ?? $this->eer,
        );
    }
}
