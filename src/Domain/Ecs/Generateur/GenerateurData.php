<?php

namespace App\Domain\Ecs\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $pecs,
        public readonly ?float $paux,
        public readonly ?float $pn,
        public readonly ?float $cop,
        public readonly ?float $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
    ) {}

    public static function create(
        ?float $pecs = null,
        ?float $paux = null,
        ?float $pn = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
    ): self {
        Assert::nullOrGreaterThanEq($pecs, 0);
        Assert::nullOrGreaterThanEq($paux, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($cop, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThanEq($pveilleuse, 0);

        return new self(
            pecs: $pecs,
            paux: $paux,
            pn: $pn,
            cop: $cop,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }

    public function with(
        ?float $pecs = null,
        ?float $paux = null,
        ?float $pn = null,
        ?float $cop = null,
        ?float $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
    ): self {
        return self::create(
            pecs: $pecs ?? $this->pecs,
            paux: $paux ?? $this->paux,
            pn: $pn ?? $this->pn,
            cop: $cop ?? $this->cop,
            rpn: $rpn ?? $this->rpn,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
        );
    }
}
