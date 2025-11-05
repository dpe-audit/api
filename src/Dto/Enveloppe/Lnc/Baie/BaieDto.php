<?php

namespace App\Dto\Enveloppe\Lnc\Baie;

use App\Domain\Enveloppe\Lnc\Baie\{Baie, Materiau, TypeVitrage};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/local_non_chauffe/baie.yaml
 */
final class BaieDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?Materiau $materiau,
        public readonly TypeVitrage $type_vitrage,
        public readonly ?bool $presence_rupteur_pont_thermique,
        public readonly PositionDto $position,
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
