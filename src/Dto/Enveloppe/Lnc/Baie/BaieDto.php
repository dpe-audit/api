<?php

namespace App\Dto\Enveloppe\Lnc\Baie;

use App\Domain\Enveloppe\Lnc\Baie\Baie;
use App\Domain\Enveloppe\Lnc\Baie\BaieCollection;
use App\Domain\Enveloppe\Lnc\Baie\Materiau;
use App\Domain\Enveloppe\Lnc\Baie\TypeVitrage;

final class BaieDto
{
    public function __construct(
        public string $id,
        public string $description,
        public ?Materiau $materiau,
        public TypeVitrage $type_vitrage,
        public ?bool $presence_rupteur_pont_thermique,
        public PositionDto $position,
    ) {}

    public static function from(Baie $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            materiau: $data->materiau(),
            type_vitrage: $data->type_vitrage(),
            presence_rupteur_pont_thermique: $data->presence_rupteur_pont_thermique(),
            position: PositionDto::from($data->position()),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(BaieCollection $data): array
    {
        return $data->map(fn(Baie $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'materiau' => $this->materiau?->value,
            'type_vitrage' => $this->type_vitrage->value,
            'presence_rupteur_pont_thermique' => $this->presence_rupteur_pont_thermique,
            'position' => $this->position->__normalize(),
        ];
    }
}
