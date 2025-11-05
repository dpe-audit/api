<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Installation\{Installation, TypeVentilation};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ventilation/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly TypeVentilation $type,
        public readonly ?string $generateur_id,
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
