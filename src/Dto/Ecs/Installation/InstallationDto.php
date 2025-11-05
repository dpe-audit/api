<?php

namespace App\Dto\Ecs\Installation;

use App\Domain\Ecs\Installation\Installation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly ?SolaireThermiqueDto $solaire_thermique,
    ) {}

    public static function from(Installation $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
            solaire_thermique: $data->solaire_thermique() ? SolaireThermiqueDto::from($data->solaire_thermique()) : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'solaire_thermique' => $this->solaire_thermique?->__normalize(),
        ];
    }
}
