<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetre;
use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetreCollection;
use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;

final class DoubleFenetreDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeBaie $type,
        public ?float $ug,
        public ?float $uw,
        public ?float $sw,
        public PositionDto $position,
        public VitrageDto $vitrage,
        public ?SurvitrageDto $survitrage,
        public ?MenuiserieDto $menuiserie,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(DoubleFenetreCollection $data): array
    {
        return $data->map(fn(DoubleFenetre $item) => self::from($item))->values();
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
