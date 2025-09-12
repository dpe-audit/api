<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Systeme;
use App\Domain\Ecs\Systeme\SystemeCollection;

final class SystemeDto
{
    public function __construct(
        public string $id,
        public string $description,
        public string $generateur_id,
        public string $installation_id,
        public ReseauDto $reseau,
        public StockageDto $stockage,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(SystemeCollection $data): array
    {
        return $data->map(fn(Systeme $item) => self::from($item))->values();
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
