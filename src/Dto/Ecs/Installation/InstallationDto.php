<?php

namespace App\Dto\Ecs\Installation;

use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Installation\InstallationCollection;

final class InstallationDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface,
        public ?SolaireThermiqueDto $solaire_thermique,
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
            'solaire_thermique' => $this->solaire_thermique?->__normalize(),
        ];
    }
}
