<?php

namespace App\Domain\Ecs\Systeme;

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
        public readonly ?float $cef_ecs,
        public readonly ?float $cep_ecs,
        public readonly ?float $eges_ecs,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
        public readonly ?float $pertes_stockage,
        public readonly ?float $pertes_stockage_recuperables,
        public readonly ?float $pertes_distribution,
        public readonly ?float $pertes_distribution_recuperables,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($iecs, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($rs, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($rgs, 0);
        Assert::nullOrGreaterThanEq($cef_ecs, 0);
        Assert::nullOrGreaterThanEq($cep_ecs, 0);
        Assert::nullOrGreaterThanEq($eges_ecs, 0);
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);
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
            cef_ecs: $cef_ecs,
            cep_ecs: $cep_ecs,
            eges_ecs: $eges_ecs,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
            pertes_stockage: $pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            iecs: $iecs ?? $this->iecs,
            rd: $rd ?? $this->rd,
            rs: $rs ?? $this->rs,
            rg: $rg ?? $this->rg,
            rgs: $rgs ?? $this->rgs,
            cef_ecs: $cef_ecs ?? $this->cef_ecs,
            cep_ecs: $cep_ecs ?? $this->cep_ecs,
            eges_ecs: $eges_ecs ?? $this->eges_ecs,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
            pertes_stockage: $pertes_stockage ?? $this->pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables ?? $this->pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution ?? $this->pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables ?? $this->pertes_distribution_recuperables,
        );
    }
}
