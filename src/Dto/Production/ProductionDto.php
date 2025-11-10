<?php

namespace App\Dto\Production;

use App\Domain\Production\{Production, ProductionData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/production/production.yaml
 * 
 * @property array<PanneauPhotovoltaiqueDto> $panneaux_photovoltaiques
 */
final class ProductionDto
{
    public function __construct(
        public readonly array $panneaux_photovoltaiques,

        public readonly ?ProductionData $data = null,
    ) {}

    public static function from(Production $entity): self
    {
        return new self(
            panneaux_photovoltaiques: $entity->panneaux_photovoltaiques()->map(fn($item) => PanneauPhotovoltaiqueDto::from($item))->values(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'panneaux_photovoltaiques' => array_values(array_map(fn($item): array => $item->__normalize(), $this->panneaux_photovoltaiques)),
        ];
        if ($this->data) {
            $data['data'] = [
                'ppv' => $this->data->ppv,
            ];
        }
        return $data;
    }
}
