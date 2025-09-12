<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Ventilation\Generateur\TypeGenerateur;
use App\Domain\Ventilation\Generateur\TypeVmc;

final class GenerateurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeGenerateur $type,
        public ?TypeVmc $type_vmc,
        public bool $generateur_collectif,
        public ?bool $presence_echangeur_thermique,
        public ?int $annee_installation,
    ) {}

    public static function from(Generateur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: (string) $data->description(),
            type: $data->type(),
            type_vmc: $data->type_vmc(),
            generateur_collectif: $data->generateur_collectif(),
            presence_echangeur_thermique: $data->presence_echangeur_thermique(),
            annee_installation: $data->annee_installation(),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(GenerateurCollection $data): array
    {
        return $data->map(fn(Generateur $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'type_vmc' => $this->type_vmc?->value,
            'generateur_collectif' => $this->generateur_collectif,
            'presence_echangeur_thermique' => $this->presence_echangeur_thermique,
            'annee_installation' => $this->annee_installation,
        ];
    }
}
