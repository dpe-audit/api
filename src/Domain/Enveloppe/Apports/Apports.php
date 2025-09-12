<?php

namespace App\Domain\Enveloppe\Apports;

use Webmozart\Assert\Assert;

final class Apports
{
    public function __construct(
        public readonly ?float $f,
        public readonly ?float $apport,
        public readonly ?float $apport_interne,
        public readonly ?float $apport_solaire,
        public readonly ?float $apport_fr,
        public readonly ?float $apport_interne_fr,
        public readonly ?float $apport_solaire_fr,
    ) {}

    public static function create(
        ?float $f = null,
        ?float $apport = null,
        ?float $apport_interne = null,
        ?float $apport_solaire = null,
        ?float $apport_fr = null,
        ?float $apport_interne_fr = null,
        ?float $apport_solaire_fr = null,
    ): self {
        Assert::nullOrGreaterThanEq($f, 0);
        Assert::nullOrGreaterThanEq($apport, 0);
        Assert::nullOrGreaterThanEq($apport_interne, 0);
        Assert::nullOrGreaterThanEq($apport_solaire, 0);
        Assert::nullOrGreaterThanEq($apport_fr, 0);
        Assert::nullOrGreaterThanEq($apport_interne_fr, 0);
        Assert::nullOrGreaterThanEq($apport_solaire_fr, 0);

        return new self(
            f: $f,
            apport: $apport,
            apport_interne: $apport_interne,
            apport_solaire: $apport_solaire,
            apport_fr: $apport_fr,
            apport_interne_fr: $apport_interne_fr,
            apport_solaire_fr: $apport_solaire_fr
        );
    }

    public function with(
        ?float $f = null,
        ?float $apport = null,
        ?float $apport_interne = null,
        ?float $apport_solaire = null,
        ?float $apport_fr = null,
        ?float $apport_interne_fr = null,
        ?float $apport_solaire_fr = null,
    ): self {
        return static::create(
            f: $f ?? $this->f,
            apport: $apport ?? $this->apport,
            apport_interne: $apport_interne ?? $this->apport_interne,
            apport_solaire: $apport_solaire ?? $this->apport_solaire,
            apport_fr: $apport_fr ?? $this->apport_fr,
            apport_interne_fr: $apport_interne_fr ?? $this->apport_interne_fr,
            apport_solaire_fr: $apport_solaire_fr ?? $this->apport_solaire_fr
        );
    }
}
