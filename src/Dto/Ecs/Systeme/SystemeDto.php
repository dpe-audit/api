<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\{Systeme, SystemeData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/systeme.yaml
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $generateur_id,
        public readonly string $installation_id,
        public readonly ReseauDto $reseau,
        public readonly StockageDto $stockage,
        public readonly ?SystemeData $data = null,
    ) {}

    public static function from(Systeme $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            generateur_id: (string) $entity->generateur()->id(),
            installation_id: (string) $entity->installation()->id(),
            reseau: ReseauDto::from($entity->reseau()),
            stockage: StockageDto::from($entity->stockage()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'generateur_id' => $this->generateur_id,
            'installation_id' => $this->installation_id,
            'reseau' => $this->reseau->__normalize(),
            'stockage' => $this->stockage->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'iecs' => $this->data->iecs,
                'rd' => $this->data->rd,
                'rs' => $this->data->rs,
                'rg' => $this->data->rg,
                'rgs' => $this->data->rgs,
                'pertes_stockage' => $this->data->pertes_stockage,
                'pertes_stockage_recuperables' => $this->data->pertes_stockage_recuperables,
                'pertes_distribution' => $this->data->pertes_distribution,
                'pertes_distribution_recuperables' => $this->data->pertes_distribution_recuperables,
                'consommations' => $this->data->consommations?->__normalize(),
            ];
        }
        return $data;
    }
}
