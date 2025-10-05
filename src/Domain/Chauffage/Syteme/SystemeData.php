<?php

namespace App\Domain\Chauffage\Systeme;

use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?Configuration $configuration,
        public readonly ?float $rdim,
        public readonly ?float $i0,
        public readonly ?float $int,
        public readonly ?float $ich,
        public readonly ?float $re,
        public readonly ?float $rd,
        public readonly ?float $rg,
        public readonly ?float $rr,
        public readonly ?float $cef_ch,
        public readonly ?float $cep_ch,
        public readonly ?float $eges_ch,
        public readonly ?float $cef_aux,
        public readonly ?float $cep_aux,
        public readonly ?float $eges_aux,
        public readonly ?Pertes $pertes,
    ) {}

    public static function create(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?Pertes $pertes = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($i0, 0);
        Assert::nullOrGreaterThanEq($int, 0);
        Assert::nullOrGreaterThanEq($ich, 0);
        Assert::nullOrGreaterThanEq($re, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($rr, 0);
        Assert::nullOrGreaterThanEq($cef_ch, 0);
        Assert::nullOrGreaterThanEq($cep_ch, 0);
        Assert::nullOrGreaterThanEq($eges_ch, 0);
        Assert::nullOrGreaterThanEq($cef_aux, 0);
        Assert::nullOrGreaterThanEq($cep_aux, 0);
        Assert::nullOrGreaterThanEq($eges_aux, 0);

        return new self(
            configuration: $configuration,
            rdim: $rdim,
            i0: $i0,
            int: $int,
            ich: $ich,
            re: $re,
            rd: $rd,
            rg: $rg,
            rr: $rr,
            cef_ch: $cef_ch,
            cep_ch: $cep_ch,
            eges_ch: $eges_ch,
            cef_aux: $cef_aux,
            cep_aux: $cep_aux,
            eges_aux: $eges_aux,
            pertes: $pertes,
        );
    }

    public function with(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?float $cef_ch = null,
        ?float $cep_ch = null,
        ?float $eges_ch = null,
        ?float $cef_aux = null,
        ?float $cep_aux = null,
        ?float $eges_aux = null,
        ?Pertes $pertes = null,
    ): self {
        return self::create(
            configuration: $configuration ?? $this->configuration,
            rdim: $rdim ?? $this->rdim,
            i0: $i0 ?? $this->i0,
            int: $int ?? $this->int,
            ich: $ich ?? $this->ich,
            re: $re ?? $this->re,
            rd: $rd ?? $this->rd,
            rg: $rg ?? $this->rg,
            rr: $rr ?? $this->rr,
            cef_ch: $cef_ch ?? $this->cef_ch,
            cep_ch: $cep_ch ?? $this->cep_ch,
            eges_ch: $eges_ch ?? $this->eges_ch,
            cef_aux: $cef_aux ?? $this->cef_aux,
            cep_aux: $cep_aux ?? $this->cep_aux,
            eges_aux: $eges_aux ?? $this->eges_aux,
            pertes: $pertes ?? $this->pertes,
        );
    }
}
