<?php

namespace App\Domain\Ventilation\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $ratio_utilisation,
        public readonly ?float $pvent_moy,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::greaterThanEq($ratio_utilisation, 0);
        Assert::greaterThanEq($pvent_moy, 0);
        Assert::greaterThanEq($cef_aux, 0);
        Assert::greaterThanEq($cep_aux, 0);
        Assert::greaterThanEq($eges_aux, 0);

        return new self(
            rdim: $rdim,
            ratio_utilisation: $ratio_utilisation,
            pvent_moy: $pvent_moy,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            ratio_utilisation: $ratio_utilisation ?? $this->ratio_utilisation,
            pvent_moy: $pvent_moy ?? $this->pvent_moy,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
        );
    }
}
