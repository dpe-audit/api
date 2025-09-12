<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Installation\{Installation, InstallationCollection};

final class InstallationDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface,
    ) {}

    public static function from(Installation $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
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
        ];
    }
}
