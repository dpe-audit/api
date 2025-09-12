<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Lnc\Lnc;

final class Baie
{
    private BaieData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private TypeVitrage $type_vitrage,
        private ?Materiau $materiau,
        private ?bool $presence_rupteur_pont_thermique,
        private Position $position,
    ) {
        $this->data = BaieData::create();
    }

    public static function create(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        TypeVitrage $type_vitrage,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique,
        Position $position,
    ): self {
        return new self(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            type_vitrage: $type_vitrage,
            materiau: $materiau,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
            position: $position,
        );
    }

    public function reinitialise(): self
    {
        $this->data = BaieData::create();
        return $this;
    }

    public function calcule(BaieData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function local_non_chauffe(): Lnc
    {
        return $this->local_non_chauffe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->type_vitrage;
    }

    public function materiau(): ?Materiau
    {
        return $this->materiau;
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return $this->presence_rupteur_pont_thermique;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function data(): BaieData
    {
        return $this->data;
    }
}
