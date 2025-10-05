<?php

namespace App\Domain\Refroidissement;

use Webmozart\Assert\Assert;
use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Emission\EmissionCollection;

final class RefroidissementData
{
    public function __construct(
        public readonly ?float $bfr,
        public readonly ?ConsommationCollection $consommations,
        public readonly ?EmissionCollection $emissions,
    ) {}

    public static function create(
        ?float $bfr = null,
        ?ConsommationCollection $consommations = null,
        ?EmissionCollection $emissions = null,
    ): self {
        Assert::nullOrGreaterThanEq($bfr, 0);

        return new self(
            bfr: $bfr,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $bfr = null,
        ?ConsommationCollection $consommations = null,
        ?EmissionCollection $emissions = null,
    ): self {
        return self::create(
            bfr: $bfr ?? $this->bfr,
            consommations: $consommations ?? $this->consommations,
            emissions: $emissions ?? $this->emissions,
        );
    }
}
