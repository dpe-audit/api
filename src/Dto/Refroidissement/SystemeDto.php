<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Systeme\Systeme;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/systeme.yaml
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $installation_id,
        public readonly string $generateur_id,
    ) {}

    public static function from(Systeme $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            installation_id: (string) $data->installation()->id(),
            generateur_id: (string) $data->generateur()->id(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'installation_id' => $this->installation_id,
            'generateur_id' => $this->generateur_id,
        ];
    }
}
