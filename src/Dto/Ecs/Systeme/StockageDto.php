<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Stockage\Stockage;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/systeme.yaml
 */
final class StockageDto
{
    public function __construct(
        public readonly ?float $volume,
        public readonly ?bool $position_volume_chauffe,
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
