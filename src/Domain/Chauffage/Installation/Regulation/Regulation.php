<?php

namespace App\Domain\Chauffage\Installation\Regulation;

final class Regulation
{
    public function __construct(
        public readonly bool $presence_regulation,
        public readonly bool $minimum_temperature,
        public readonly bool $detection_presence,
        public readonly bool $intermittence,
    ) {}

    public static function create(
        bool $presence_regulation = false,
        bool $minimum_temperature = false,
        bool $detection_presence = false,
    ): self {
        return new self(
            presence_regulation: $presence_regulation,
            minimum_temperature: $presence_regulation && $minimum_temperature,
            detection_presence: $presence_regulation && $detection_presence,
            intermittence: $presence_regulation && ($minimum_temperature || $detection_presence),
        );
    }
}
