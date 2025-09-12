<?php

namespace App\Domain\Ventilation\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $ratio_utilisation,
        public readonly ?float $pvent_moy,
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::greaterThanEq($ratio_utilisation, 0);
        Assert::greaterThanEq($pvent_moy, 0);
        Assert::greaterThanEq($cef, 0);
        Assert::greaterThanEq($cep, 0);
        Assert::greaterThanEq($eges, 0);

        return new self(
            ratio_utilisation: $ratio_utilisation,
            pvent_moy: $pvent_moy,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }

    public function with(
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        return new self(
            ratio_utilisation: $ratio_utilisation ?? $this->ratio_utilisation,
            pvent_moy: $pvent_moy ?? $this->pvent_moy,
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
        );
    }
}
