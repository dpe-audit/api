<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Systeme;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/systeme.yaml
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $generateur_id,
        public readonly string $installation_id,
        public readonly ReseauDto $reseau,
        public readonly StockageDto $stockage,
    ) {}

    public static function from(Systeme $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            generateur_id: (string) $data->generateur()->id(),
            installation_id: (string) $data->installation()->id(),
            reseau: ReseauDto::from($data->reseau()),
            stockage: StockageDto::from($data->stockage()),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'generateur_id' => $this->generateur_id,
            'installation_id' => $this->installation_id,
            'reseau' => $this->reseau->__normalize(),
            'stockage' => $this->stockage->__normalize(),
        ];
    }
}
