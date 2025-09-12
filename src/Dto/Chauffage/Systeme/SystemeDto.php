<?php

namespace App\Dto\Chauffage\Systeme;

use App\Domain\Chauffage\Emetteur\Emetteur;
use App\Domain\Chauffage\Systeme\Systeme;
use App\Domain\Chauffage\Systeme\SystemeCollection;
use App\Domain\Chauffage\TypeChauffage;

/**
 * @property array<string> $emetteurs
 */
final class SystemeDto
{
    public function __construct(
        public string $id,
        public string $description,
        public string $generateur_id,
        public string $installation_id,
        public TypeChauffage $type,
        public ReseauDto $reseau,
        public array $emetteurs,
    ) {}

    public static function from(Systeme $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            generateur_id: (string) $data->generateur()->id(),
            installation_id: (string) $data->installation()->id(),
            type: $data->type(),
            reseau: ReseauDto::from($data->reseau()),
            emetteurs: $data->emetteurs()->map(fn(Emetteur $item) => (string) $item->id())->values(),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(SystemeCollection $data): array
    {
        return $data->map(fn(Systeme $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'generateur_id' => $this->generateur_id,
            'installation_id' => $this->installation_id,
            'type' => $this->type->value,
            'reseau' => $this->reseau->__normalize(),
            'emetteurs' => $this->emetteurs,
        ];
    }
}
