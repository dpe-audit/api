<?php

namespace App\Domain\Common\Bilan;

use Webmozart\Assert\Assert;

final class Bilan
{
    public function __construct(
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
        public readonly ?EtiquetteEnergie $etiquette_energie,
        public readonly ?EtiquetteClimat $etiquette_climat,
    ) {}

    public static function create(
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
        ?EtiquetteEnergie $etiquette_energie = null,
        ?EtiquetteClimat $etiquette_climat = null,
    ): self {
        Assert::nullOrGreaterThanEq($cef, 0);
        Assert::nullOrGreaterThanEq($cep, 0);
        Assert::nullOrGreaterThanEq($eges, 0);
        return new self(
            cef: $cef,
            cep: $cep,
            eges: $eges,
            etiquette_energie: $etiquette_energie,
            etiquette_climat: $etiquette_climat,
        );
    }

    public function with(
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
        ?EtiquetteEnergie $etiquette_energie = null,
        ?EtiquetteClimat $etiquette_climat = null,
    ): self {
        return self::create(
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
            etiquette_energie: $etiquette_energie ?? $this->etiquette_energie,
            etiquette_climat: $etiquette_climat ?? $this->etiquette_climat,
        );
    }
}
