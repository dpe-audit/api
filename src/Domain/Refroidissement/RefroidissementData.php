<?php

namespace App\Domain\Refroidissement;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class RefroidissementData
{
    public function __construct(
        public readonly ?float $bfr,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $bfr = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThanEq($bfr, 0);

        return new self(
            bfr: $bfr,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $bfr = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            bfr: $bfr ?? $this->bfr,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
