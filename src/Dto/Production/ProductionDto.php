<?php

namespace App\Dto\Production;

use App\Domain\Production\Production;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/production/production.yaml
 * 
 * @property array<PanneauPhotovoltaiqueDto> $panneaux_photovoltaiques
 */
final class ProductionDto
{
    public function __construct(
        public readonly array $panneaux_photovoltaiques,
    ) {}

    public static function from(Production $data): self
    {
        return new self(
            panneaux_photovoltaiques: $data->panneaux_photovoltaiques()->map(fn($item) => PanneauPhotovoltaiqueDto::from($item))->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'panneaux_photovoltaiques' => array_map(fn($item): array => $item->__normalize(), $this->panneaux_photovoltaiques),
        ];
    }
}
