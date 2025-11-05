<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\{DoubleFenetre, TypeBaie};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/double_fenetre.yaml
 */
final class DoubleFenetreDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeBaie $type,
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $sw,
        public readonly PositionDto $position,
        public readonly VitrageDto $vitrage,
        public readonly ?SurvitrageDto $survitrage,
        public readonly ?MenuiserieDto $menuiserie,
    ) {}

    public static function from(DoubleFenetre $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            ug: $data->ug(),
            uw: $data->uw(),
            sw: $data->sw(),
            position: PositionDto::from($data->position()),
            vitrage: VitrageDto::from($data->vitrage()),
            survitrage: $data->survitrage() ? SurvitrageDto::from($data->survitrage()) : null,
            menuiserie: $data->menuiserie() ? MenuiserieDto::from($data->menuiserie()) : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'ug' => $this->ug,
            'uw' => $this->uw,
            'sw' => $this->sw,
            'position' => $this->position->__normalize(),
            'vitrage' => $this->vitrage->__normalize(),
            'survitrage' => $this->survitrage?->__normalize(),
            'menuiserie' => $this->menuiserie?->__normalize(),
        ];
    }
}
