<?php

namespace App\Domain\Logement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Diagnostic\Diagnostic;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/logement.yaml
 */
final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Diagnostic $diagnostic,
        private string $description,
        private float $surface_habitable,
        private float $hauteur_sous_plafond,
        private Position $position,
        private Typologie $typologie,
    ) {}

    public static function create(
        Id $id,
        Diagnostic $diagnostic,
        string $description,
        float $surface_habitable,
        float $hauteur_sous_plafond,
        Position $position,
        Typologie $typologie,
    ): self {
        return new self(
            id: $id,
            diagnostic: $diagnostic,
            description: $description,
            position: $position,
            typologie: $typologie,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function diagnostic(): Diagnostic
    {
        return $this->diagnostic;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface_habitable(): float
    {
        return $this->surface_habitable;
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->hauteur_sous_plafond;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function typologie(): Typologie
    {
        return $this->typologie;
    }
}
