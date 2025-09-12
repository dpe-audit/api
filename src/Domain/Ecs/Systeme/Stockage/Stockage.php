<?php

namespace App\Domain\Ecs\Systeme\Stockage;

final class Stockage
{
    public function __construct(
        public readonly ?float $volume,
        public readonly ?bool $position_volume_chauffe,
    ) {}

    public static function create(?float $volume, ?bool $position_volume_chauffe): self
    {
        return new self(
            volume: $volume,
            position_volume_chauffe: $position_volume_chauffe,
        );
    }
}
