<?php

namespace App\Domain\Reseau;

final class Reseau
{
    public function __construct(
        private readonly string $id,
        private Float $contenu_co2,
        private Float $contenu_co2_acv,
        private Float $taux_enr,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function contenu_co2(): Float
    {
        return $this->contenu_co2;
    }

    public function contenu_co2_acv(): Float
    {
        return $this->contenu_co2_acv;
    }

    public function taux_enr(): Float
    {
        return $this->taux_enr;
    }
}
