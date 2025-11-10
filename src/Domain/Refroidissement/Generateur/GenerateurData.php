<?php

namespace App\Domain\Refroidissement\Generateur;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $eer,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $eer = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThan($eer, 0);

        return new self(
            rdim: $rdim,
            eer: $eer,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $eer = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            eer: $eer ?? $this->eer,
            consommations: $consommations ?? $this->consommations
        );
    }
}
