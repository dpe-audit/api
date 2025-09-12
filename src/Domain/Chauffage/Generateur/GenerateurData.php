<?php

namespace App\Domain\Chauffage\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $pch,
        public readonly ?float $pn,
        public readonly ?float $paux,
        public readonly ?float $scop,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
    ) {}

    public static function create(
        ?float $pch = null,
        ?float $pn = null,
        ?float $paux = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
    ): self {
        Assert::greaterThanEq($pch, 0);
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($paux, 0);
        Assert::greaterThanEq($scop, 0);
        Assert::greaterThanEq($rpn, 0);
        Assert::greaterThanEq($rpint, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);
        Assert::greaterThanEq($tfonc30, 0);
        Assert::greaterThanEq($tfonc100, 0);

        return new self(
            pch: $pch,
            pn: $pn,
            paux: $paux,
            scop: $scop,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
        );
    }

    public function with(
        ?float $pch = null,
        ?float $pn = null,
        ?float $paux = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?Float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
    ): self {
        return self::create(
            pch: $pch ?? $this->pch,
            pn: $pn ?? $this->pn,
            paux: $paux ?? $this->paux,
            scop: $scop ?? $this->scop,
            rpn: $rpn ?? $this->rpn,
            rpint: $rpint ?? $this->rpint,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            tfonc30: $tfonc30 ?? $this->tfonc30,
            tfonc100: $tfonc100 ?? $this->tfonc100,
        );
    }
}
