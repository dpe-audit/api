<?php

namespace App\Domain\Ecs\Systeme;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $iecs,
        public readonly ?float $rd,
        public readonly ?float $rs,
        public readonly ?float $rg,
        public readonly ?float $rgs,
        public readonly ?float $pertes_stockage,
        public readonly ?float $pertes_stockage_recuperables,
        public readonly ?float $pertes_distribution,
        public readonly ?float $pertes_distribution_recuperables,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($iecs, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($rs, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($rgs, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution_recuperables, 0);

        return new self(
            rdim: $rdim,
            iecs: $iecs,
            rd: $rd,
            rs: $rs,
            rg: $rg,
            rgs: $rgs,
            pertes_stockage: $pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            iecs: $iecs ?? $this->iecs,
            rd: $rd ?? $this->rd,
            rs: $rs ?? $this->rs,
            rg: $rg ?? $this->rg,
            rgs: $rgs ?? $this->rgs,
            pertes_stockage: $pertes_stockage ?? $this->pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables ?? $this->pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution ?? $this->pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables ?? $this->pertes_distribution_recuperables,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
