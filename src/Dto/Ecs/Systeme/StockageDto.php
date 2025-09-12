<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Stockage\Stockage;

final class StockageDto
{
    public function __construct(
        public ?float $volume,
        public ?bool $position_volume_chauffe,
    ) {}

    public static function from(Stockage $data): self
    {
        return new self(
            volume: $data->volume,
            position_volume_chauffe: $data->position_volume_chauffe,
        );
    }

    public function __normalize(): array
    {
        return [
            'volume' => $this->volume,
            'position_volume_chauffe' => $this->position_volume_chauffe,
        ];
    }
}
