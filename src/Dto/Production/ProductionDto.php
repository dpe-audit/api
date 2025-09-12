<?php

namespace App\Dto\Production;

use App\Domain\Production\Production;

/**
 * @property array<PanneauPhotovoltaiqueDto> $panneaux_photovoltaiques
 */
final class ProductionDto
{
    public function __construct(
        public array $panneaux_photovoltaiques,
    ) {}

    public static function from(Production $data): self
    {
        return new self(
            panneaux_photovoltaiques: PanneauPhotovoltaiqueDto::fromCollection($data->panneaux_photovoltaiques()),
        );
    }

    public function __normalize(): array
    {
        return [
            'panneaux_photovoltaiques' => array_map(fn(PanneauPhotovoltaiqueDto $item): array => $item->__normalize(), $this->panneaux_photovoltaiques),
        ];
    }
}
