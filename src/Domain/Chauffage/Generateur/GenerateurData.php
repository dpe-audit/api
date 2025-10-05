<?php

namespace App\Domain\Chauffage\Generateur;

use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $cef_ch,
        public readonly ?float $cep_ch,
        public readonly ?float $eges_ch,
        public readonly ?float $pn,
        public readonly ?float $pdim,
        public readonly ?float $pch,
        public readonly ?float $paux,
        public readonly ?float $scop,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
        public readonly ?Pertes $pertes,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pch = null,
        ?float $paux = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?Pertes $pertes = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($cef_ch, 0);
        Assert::nullOrGreaterThanEq($cep_ch, 0);
        Assert::nullOrGreaterThanEq($eges_ch, 0);
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($pdim, 0);
        Assert::greaterThanEq($pch, 0);
        Assert::greaterThanEq($paux, 0);
        Assert::greaterThanEq($scop, 0);
        Assert::greaterThanEq($rpn, 0);
        Assert::greaterThanEq($rpint, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);
        Assert::greaterThanEq($tfonc30, 0);
        Assert::greaterThanEq($tfonc100, 0);

        return new self(
            rdim: $rdim,
            cef_ch: $cef_ch,
            cep_ch: $cep_ch,
            eges_ch: $eges_ch,
            pn: $pn,
            pdim: $pdim,
            pch: $pch,
            paux: $paux,
            scop: $scop,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
            pertes: $pertes,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $pn = null,
        ?float $pdim = null,
        ?float $pch = null,
        ?float $paux = null,
        ?float $scop = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?Pertes $pertes = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            cef_ch: $cef_ch ?? $this->cef_ch,
            cep_ch: $cep_ch ?? $this->cep_ch,
            eges_ch: $eges_ch ?? $this->eges_ch,
            pn: $pn ?? $this->pn,
            pdim: $pdim ?? $this->pdim,
            pch: $pch ?? $this->pch,
            paux: $paux ?? $this->paux,
            scop: $scop ?? $this->scop,
            rpn: $rpn ?? $this->rpn,
            rpint: $rpint ?? $this->rpint,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            tfonc30: $tfonc30 ?? $this->tfonc30,
            tfonc100: $tfonc100 ?? $this->tfonc100,
            pertes: $pertes ?? $this->pertes,
        );
    }
}
