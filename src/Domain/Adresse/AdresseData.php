<?php

namespace App\Domain\Adresse;

final class AdresseData
{
    public function __construct(
        public readonly ?ZoneClimatique $zone_climatique,
    ) {}

    public static function create(
        ?ZoneClimatique $zone_climatique = null,
    ): self {
        return new self(
            zone_climatique: $zone_climatique,
        );
    }

    public function with(
        ?ZoneClimatique $zone_climatique = null,
    ): self {
        return new self(
            zone_climatique: $zone_climatique ?? $this->zone_climatique,
        );
    }
}
