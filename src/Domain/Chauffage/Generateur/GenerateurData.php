<?php

namespace App\Domain\Chauffage\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $pn,
        public readonly ?float $pdim,
        public readonly ?float $pch,
        public readonly ?float $scop,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
        public readonly ?float $pertes_generation,
        public readonly ?float $pertes_generation_recuperables,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pch = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($pdim, 0);
        Assert::greaterThanEq($pch, 0);
        Assert::greaterThanEq($scop, 0);
        Assert::greaterThanEq($rpn, 0);
        Assert::greaterThanEq($rpint, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);
        Assert::greaterThanEq($tfonc30, 0);
        Assert::greaterThanEq($tfonc100, 0);
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);

        return new self(
            rdim: $rdim,
            pn: $pn,
            pdim: $pdim,
            pch: $pch,
            scop: $scop,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pch = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            pn: $pn ?? $this->pn,
            pdim: $pdim ?? $this->pdim,
            pch: $pch ?? $this->pch,
            scop: $scop ?? $this->scop,
            rpn: $rpn ?? $this->rpn,
            rpint: $rpint ?? $this->rpint,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            tfonc30: $tfonc30 ?? $this->tfonc30,
            tfonc100: $tfonc100 ?? $this->tfonc100,
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables
        );
    }
}
