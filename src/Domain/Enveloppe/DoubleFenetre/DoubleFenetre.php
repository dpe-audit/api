<?php

namespace App\Domain\Enveloppe\DoubleFenetre;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\DoubleFenetre\Position\Position;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\Survitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\Vitrage;
use App\Domain\Enveloppe\Enveloppe;

final class DoubleFenetre
{
    private DoubleFenetreData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeBaie $type,
        private ?float $ug,
        private ?float $uw,
        private ?float $sw,
        private Position $position,
        private Vitrage $vitrage,
        private ?Survitrage $survitrage,
        private ?Menuiserie $menuiserie,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeBaie $type,
        ?float $ug,
        ?float $uw,
        ?float $sw,
        Position $position,
        Vitrage $vitrage,
        ?Survitrage $survitrage,
        ?Menuiserie $menuiserie,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            ug: $ug,
            uw: $uw,
            sw: $sw,
            position: $position,
            vitrage: $vitrage,
            survitrage: $survitrage,
            menuiserie: $menuiserie,
        );
    }

    public function reinitialise(): self
    {
        $this->data = DoubleFenetreData::create();
        return $this;
    }

    public function calcule(DoubleFenetreData $data): self
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

    public function type(): TypeBaie
    {
        return $this->type;
    }

    public function ug(): ?float
    {
        return $this->ug;
    }

    public function uw(): ?float
    {
        return $this->uw;
    }

    public function sw(): ?float
    {
        return $this->sw;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function vitrage(): Vitrage
    {
        return $this->vitrage;
    }

    public function survitrage(): ?Survitrage
    {
        return $this->survitrage;
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->menuiserie;
    }

    public function data(): DoubleFenetreData
    {
        return $this->data;
    }
}
