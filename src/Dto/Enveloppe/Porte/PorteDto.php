<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\{Isolation, Materiau, Porte};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/porte.yaml
 */
final class PorteDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?Isolation $isolation,
        public readonly ?Materiau $materiau,
        public readonly ?int $annee_installation,
        public readonly ?float $u,
        public readonly PositionDto $position,
        public readonly MenuiserieDto $menuiserie,
        public readonly VitrageDto $vitrage,
    ) {}

    public static function from(Porte $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            isolation: $data->isolation(),
            materiau: $data->materiau(),
            annee_installation: $data->annee_installation(),
            u: $data->u(),
            position: PositionDto::from($data->position()),
            menuiserie: MenuiserieDto::from($data->menuiserie()),
            vitrage: VitrageDto::from($data->vitrage()),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
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
