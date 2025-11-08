<?php

namespace App\Dto\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Systeme;
use App\Domain\Chauffage\TypeChauffage;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/systeme.yaml
 * 
 * @property array<string> $emetteurs
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $generateur_id,
        public readonly string $installation_id,
        public readonly TypeChauffage $type,
        public readonly ?int $cascade,
        public readonly ?ReseauDto $reseau,
        public readonly array $emetteurs,
    ) {}

    public static function from(Systeme $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            generateur_id: (string) $data->generateur()->id(),
            installation_id: (string) $data->installation()->id(),
            type: $data->type(),
            cascade: $data->cascade(),
            reseau: $data->reseau() ? ReseauDto::from($data->reseau()) : null,
            emetteurs: $data->emetteurs()->map(fn($item) => (string) $item->id())->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'generateur_id' => $this->generateur_id,
            'installation_id' => $this->installation_id,
            'type' => $this->type->value,
            'cascade' => $this->cascade,
            'reseau' => $this->reseau?->__normalize(),
            'emetteurs' => $this->emetteurs,
        ];
    }
}
