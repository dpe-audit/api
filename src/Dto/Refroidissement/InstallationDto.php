<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Installation\Installation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
    ) {}

    public static function from(Installation $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
        ];
    }
}
