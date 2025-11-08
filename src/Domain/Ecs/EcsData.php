<?php

namespace App\Domain\Ecs;

use Webmozart\Assert\Assert;

final class EcsData
{
    public function __construct(
        public readonly ?float $nmax,
        public readonly ?float $nadeq,
        public readonly ?float $becs,
        public readonly ?float $cef_ecs,
        public readonly ?float $cep_ecs,
        public readonly ?float $eges_ecs,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
        public readonly ?float $pertes_generation,
        public readonly ?float $pertes_generation_recuperables,
        public readonly ?float $pertes_stockage,
        public readonly ?float $pertes_stockage_recuperables,
        public readonly ?float $pertes_distribution,
        public readonly ?float $pertes_distribution_recuperables,
    ) {}

    public static function create(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        Assert::nullOrGreaterThan($nmax, 0);
        Assert::nullOrGreaterThan($nadeq, 0);
        Assert::nullOrGreaterThan($becs, 0);
        Assert::nullOrGreaterThan($cef_ecs, 0);
        Assert::nullOrGreaterThan($cep_ecs, 0);
        Assert::nullOrGreaterThan($eges_ecs, 0);
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution_recuperables, 0);

        return new self(
            nmax: $nmax,
            nadeq: $nadeq,
            becs: $becs,
            cef_ecs: $cef_ecs,
            cep_ecs: $cep_ecs,
            eges_ecs: $eges_ecs,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
            pertes_stockage: $pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables,
        );
    }

    public function with(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        return self::create(
            nmax: $nmax ?? $this->nmax,
            nadeq: $nadeq ?? $this->nadeq,
            becs: $becs ?? $this->becs,
            cef_ecs: $cef_ecs ?? $this->cef_ecs,
            cep_ecs: $cep_ecs ?? $this->cep_ecs,
            eges_ecs: $eges_ecs ?? $this->eges_ecs,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
            pertes_stockage: $pertes_stockage ?? $this->pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables ?? $this->pertes_stockage_recuperables,
            pertes_distribution: $pertes_distribution ?? $this->pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables ?? $this->pertes_distribution_recuperables,
        );
    }
}
