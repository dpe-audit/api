<?php

namespace App\Domain\Enveloppe\Niveau;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;

final class Niveau
{
    private NiveauData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $surface,
        private InertieParoi $inertie_paroi_verticale,
        private InertieParoi $inertie_plancher_haut,
        private InertieParoi $inertie_plancher_bas,
    ) {
        $this->data = NiveauData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        float $surface,
        InertieParoi $inertie_paroi_verticale,
        InertieParoi $inertie_plancher_haut,
        InertieParoi $inertie_plancher_bas,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            surface: $surface,
            inertie_paroi_verticale: $inertie_paroi_verticale,
            inertie_plancher_haut: $inertie_plancher_haut,
            inertie_plancher_bas: $inertie_plancher_bas,
        );
    }

    public function reinitialise(): self
    {
        $this->data = NiveauData::create();
        return $this;
    }

    public function calcule(NiveauData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function inertie_paroi_verticale(): InertieParoi
    {
        return $this->inertie_paroi_verticale;
    }

    public function inertie_plancher_haut(): InertieParoi
    {
        return $this->inertie_plancher_haut;
    }

    public function inertie_plancher_bas(): InertieParoi
    {
        return $this->inertie_plancher_bas;
    }

    public function data(): NiveauData
    {
        return $this->data;
    }
}
