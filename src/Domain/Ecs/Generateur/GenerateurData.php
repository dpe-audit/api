<?php

namespace App\Domain\Ecs\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $cef_ecs,
        public readonly ?float $cep_ecs,
        public readonly ?float $eges_ecs,
        public readonly ?float $pn,
        public readonly ?float $pdim,
        public readonly ?float $pecs,
        public readonly ?float $paux,
        public readonly ?float $cop,
        public readonly ?float $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly Pertes $pertes,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pecs = null,
        ?float $paux = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?Pertes $pertes = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($cef_ecs, 0);
        Assert::nullOrGreaterThanEq($cep_ecs, 0);
        Assert::nullOrGreaterThanEq($eges_ecs, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($pdim, 0);
        Assert::nullOrGreaterThanEq($pecs, 0);
        Assert::nullOrGreaterThanEq($paux, 0);
        Assert::nullOrGreaterThan($cop, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThanEq($pveilleuse, 0);

        return new self(
            rdim: $rdim,
            cef_ecs: $cef_ecs,
            cep_ecs: $cep_ecs,
            eges_ecs: $eges_ecs,
            pn: $pn,
            pdim: $pdim,
            pecs: $pecs,
            paux: $paux,
            cop: $cop,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            pertes: $pertes ?? Pertes::create(),
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $cef_ecs = null,
        ?float $cep_ecs = null,
        ?float $eges_ecs = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pecs = null,
        ?float $paux = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?Pertes $pertes = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            cef_ecs: $cef_ecs ?? $this->cef_ecs,
            cep_ecs: $cep_ecs ?? $this->cep_ecs,
            eges_ecs: $eges_ecs ?? $this->eges_ecs,
            pn: $pn ?? $this->pn,
            pdim: $pdim ?? $this->pdim,
            pecs: $pecs ?? $this->pecs,
            paux: $paux ?? $this->paux,
            cop: $cop ?? $this->cop,
            rpn: $rpn ?? $this->rpn,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            pertes: $pertes ?? $this->pertes,
        );
    }
}
