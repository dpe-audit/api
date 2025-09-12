<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Installation\{Installation, InstallationCollection};
use App\Domain\Ventilation\Installation\TypeVentilation;

final class InstallationDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface,
        public TypeVentilation $type,
        public ?string $generateur_id,
    ) {}

    public static function from(Installation $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
            type: $data->type(),
            generateur_id: $data->generateur() ? (string) $data->generateur()->id() : null,
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(InstallationCollection $data): array
    {
        return $data->map(fn(Installation $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'type' => $this->type,
            'generateur_id' => $this->generateur_id,
        ];
    }
}
