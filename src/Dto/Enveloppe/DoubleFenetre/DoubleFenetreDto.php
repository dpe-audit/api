<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\{DoubleFenetre, DoubleFenetreData, TypeBaie};

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
        public readonly ?DoubleFenetreData $data = null,
    ) {}

    public static function from(DoubleFenetre $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            ug: $entity->ug(),
            uw: $entity->uw(),
            sw: $entity->sw(),
            position: PositionDto::from($entity->position()),
            vitrage: VitrageDto::from($entity->vitrage()),
            survitrage: $entity->survitrage() ? SurvitrageDto::from($entity->survitrage()) : null,
            menuiserie: $entity->menuiserie() ? MenuiserieDto::from($entity->menuiserie()) : null,
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
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
        if ($this->data) {
            $data['data'] = [
                'ug' => $this->data->ug,
                'uw' => $this->data->uw,
                'sw' => $this->data->sw,
            ];
        }
        return $data;
    }
}
