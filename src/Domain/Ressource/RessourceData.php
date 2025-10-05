<?php

namespace App\Domain\Ressource;

use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use Webmozart\Assert\Assert;

final class RessourceData
{
    public function __construct(
        public readonly ?ZoneClimatique $zone_climatique,
        public readonly ?bool $effet_joule,
        public readonly ?bool $parois_anciennes_lourdes,
        public readonly ?float $surface_reference,
        public readonly ?float $volume_reference,
        public readonly ?Bilan $bilan,
        public readonly ?PerteCollection $pertes,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?ZoneClimatique $zone_climatique = null,
        ?bool $effet_joule = null,
        ?bool $parois_anciennes_lourdes = null,
        ?float $surface_reference = null,
        ?float $volume_reference = null,
        ?Bilan $bilan = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThan($surface_reference, 0);
        Assert::nullOrGreaterThan($volume_reference, 0);

        return new self(
            zone_climatique: $zone_climatique,
            effet_joule: $effet_joule,
            parois_anciennes_lourdes: $parois_anciennes_lourdes,
            surface_reference: $surface_reference,
            volume_reference: $volume_reference,
            bilan: $bilan,
            pertes: $pertes,
            consommations: $consommations,
        );
    }

    public function with(
        ?ZoneClimatique $zone_climatique = null,
        ?bool $effet_joule = null,
        ?bool $parois_anciennes_lourdes = null,
        ?float $surface_reference = null,
        ?float $volume_reference = null,
        ?Bilan $bilan = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            zone_climatique: $zone_climatique ?? $this->zone_climatique,
            effet_joule: $effet_joule ?? $this->effet_joule,
            parois_anciennes_lourdes: $parois_anciennes_lourdes ?? $this->parois_anciennes_lourdes,
            surface_reference: $surface_reference ?? $this->surface_reference,
            volume_reference: $volume_reference ?? $this->volume_reference,
            bilan: $bilan ?? $this->bilan,
            pertes: $pertes ?? $this->pertes,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
