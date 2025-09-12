<?php

namespace App\Domain\Enveloppe\PontThermique\Liaison;

use App\Domain\Enveloppe\Baie\Baie;
use App\Domain\Enveloppe\Mur\Mur;
use App\Domain\Enveloppe\Paroi\Paroi;
use App\Domain\Enveloppe\PlancherBas\PlancherBas;
use App\Domain\Enveloppe\PlancherHaut\PlancherHaut;
use App\Domain\Enveloppe\Porte\Porte;
use Webmozart\Assert\Assert;

final class Liaison
{
    public function __construct(
        public readonly TypeLiaison $type,
        public readonly bool $pont_thermique_partiel,
        public readonly Mur $mur,
        public readonly ?Paroi $plancher,
        public readonly ?Paroi $ouverture,
    ) {}

    public static function create(
        TypeLiaison $type,
        bool $pont_thermique_partiel,
        Mur $mur,
        ?Paroi $plancher,
        ?Paroi $ouverture,
    ): self {
        Assert::nullOrIsInstanceOfAny($plancher, [PlancherBas::class, PlancherHaut::class]);
        Assert::nullOrIsInstanceOfAny($ouverture, [Baie::class, Porte::class]);

        return new self(
            type: $type,
            pont_thermique_partiel: $pont_thermique_partiel,
            mur: $mur,
            plancher: $plancher,
            ouverture: $ouverture,
        );
    }

    public function plancher_bas(): ?PlancherBas
    {
        return  $this->plancher && $this->plancher instanceof PlancherBas
            ? $this->plancher
            : null;
    }

    public function plancher_haut(): ?PlancherHaut
    {
        return $this->plancher && $this->plancher instanceof PlancherHaut
            ? $this->plancher
            : null;
    }

    public function baie(): ?Baie
    {
        return $this->ouverture && $this->ouverture instanceof Baie
            ? $this->ouverture
            : null;
    }

    public function porte(): ?Porte
    {
        return $this->ouverture && $this->ouverture instanceof Porte
            ? $this->ouverture
            : null;
    }
}
