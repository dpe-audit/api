<?php

namespace App\Domain\Ecs\Generateur;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $pn,
        public readonly ?float $pdim,
        public readonly ?float $pecs,
        public readonly ?float $cop,
        public readonly ?float $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $pertes_generation,
        public readonly ?float $pertes_generation_recuperables,
        public readonly ?float $pertes_stockage,
        public readonly ?float $pertes_stockage_recuperables,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pecs = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?ConsommationCollection $consommations = null
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($pdim, 0);
        Assert::nullOrGreaterThanEq($pecs, 0);
        Assert::nullOrGreaterThan($cop, 0);
        Assert::nullOrGreaterThan($rpn, 0);
        Assert::nullOrGreaterThanEq($qp0, 0);
        Assert::nullOrGreaterThanEq($pveilleuse, 0);
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_recuperables, 0);

        return new self(
            rdim: $rdim,
            pn: $pn,
            pdim: $pdim,
            pecs: $pecs,
            cop: $cop,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
            pertes_stockage: $pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pecs = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage = null,
        ?float $pertes_stockage_recuperables = null,
        ?ConsommationCollection $consommations = null
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            pn: $pn ?? $this->pn,
            pdim: $pdim ?? $this->pdim,
            pecs: $pecs ?? $this->pecs,
            cop: $cop ?? $this->cop,
            rpn: $rpn ?? $this->rpn,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
            pertes_stockage: $pertes_stockage ?? $this->pertes_stockage,
            pertes_stockage_recuperables: $pertes_stockage_recuperables ?? $this->pertes_stockage_recuperables,
            consommations: $consommations ?? $this->consommations
        );
    }
}
