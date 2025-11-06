<?php

namespace App\Domain\Chauffage;

use Webmozart\Assert\Assert;

final class ChauffageData
{
    public function __construct(
        public readonly ?float $bch,
        public readonly ?float $cef_ch,
        public readonly ?float $cep_ch,
        public readonly ?float $eges_ch,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
        public readonly ?float $pertes_generation,
        public readonly ?float $pertes_generation_recuperables,
    ) {}

    public static function create(
        ?float $bch = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($bch, 0);
        Assert::nullOrGreaterThanEq($cef_ch, 0);
        Assert::nullOrGreaterThanEq($cep_ch, 0);
        Assert::nullOrGreaterThanEq($eges_ch, 0);
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);

        return new self(
            bch: $bch,
            cef_ch: $cef_ch,
            cep_ch: $cep_ch,
            eges_ch: $eges_ch,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
        );
    }

    public function with(
        ?float $bch = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        return self::create(
            bch: $bch ?? $this->bch,
            cef_ch: $cef_ch ?? $this->cef_ch,
            cep_ch: $cep_ch ?? $this->cep_ch,
            eges_ch: $eges_ch ?? $this->eges_ch,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
        );
    }
}
