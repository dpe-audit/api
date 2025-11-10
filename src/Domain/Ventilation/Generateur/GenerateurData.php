<?php

namespace App\Domain\Ventilation\Generateur;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $ratio_utilisation,
        public readonly ?float $pvent_moy,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::greaterThanEq($ratio_utilisation, 0);

        return new self(
            rdim: $rdim,
            ratio_utilisation: $ratio_utilisation,
            pvent_moy: $pvent_moy,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $ratio_utilisation = null,
        ?float $pvent_moy = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            ratio_utilisation: $ratio_utilisation ?? $this->ratio_utilisation,
            pvent_moy: $pvent_moy ?? $this->pvent_moy,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
