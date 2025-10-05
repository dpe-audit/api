<?php

namespace App\Domain\Refroidissement\Installation;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Emission\EmissionCollection;
use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?ConsommationCollection $consommations,
        public readonly ?EmissionCollection $emissions,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?ConsommationCollection $consommations = null,
        ?EmissionCollection $emissions = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);

        return new self(
            rdim: $rdim,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $rdim = null,
        ?ConsommationCollection $consommations = null,
        ?EmissionCollection $emissions = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            consommations: $consommations ?? $this->consommations,
            emissions: $emissions ?? $this->emissions,
        );
    }
}
