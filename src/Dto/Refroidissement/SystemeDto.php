<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Systeme\{Systeme, SystemeCollection};

final class SystemeDto
{
    public function __construct(
        public string $id,
        public string $description,
        public string $installation_id,
        public string $generateur_id,
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
            'installation_id' => $this->installation_id,
            'generateur_id' => $this->generateur_id,
        ];
    }
}
