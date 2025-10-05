<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use Webmozart\Assert\Assert;

final class ChauffageData
{
    public function __construct(
        public readonly ?float $bch,
        public readonly ?PerteCollection $pertes,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $bch = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThanEq($bch, 0);
        return new self(
            bch: $bch,
            pertes: $pertes,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $bch = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            bch: $bch ?? $this->bch,
            pertes: $pertes ?? $this->pertes,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
