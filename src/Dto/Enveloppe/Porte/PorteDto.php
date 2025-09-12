<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\Isolation;
use App\Domain\Enveloppe\Porte\Materiau;
use App\Domain\Enveloppe\Porte\Porte;
use App\Domain\Enveloppe\Porte\PorteCollection;
use App\Domain\Enveloppe\Porte\TypePose;

final class PorteDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypePose $type_pose,
        public ?Isolation $isolation,
        public ?Materiau $materiau,
        public ?int $annee_installation,
        public ?float $u,
        public PositionDto $position,
        public MenuiserieDto $menuiserie,
        public VitrageDto $vitrage,
    ) {}

    public static function from(Porte $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type_pose: $data->type_pose(),
            isolation: $data->isolation(),
            materiau: $data->materiau(),
            annee_installation: $data->annee_installation(),
            u: $data->u(),
            position: PositionDto::from($data->position()),
            menuiserie: MenuiserieDto::from($data->menuiserie()),
            vitrage: VitrageDto::from($data->vitrage()),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(PorteCollection $data): array
    {
        return $data->map(fn(Porte $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type_pose' => $this->type_pose->value,
            'isolation' => $this->isolation?->value,
            'materiau' => $this->materiau?->value,
            'annee_installation' => $this->annee_installation,
            'u' => $this->u,
            'position' => $this->position->__normalize(),
            'menuiserie' => $this->menuiserie->__normalize(),
            'vitrage' => $this->vitrage->__normalize(),
        ];
    }
}
