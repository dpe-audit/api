<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, TypeGenerateur, TypeVmc};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ventilation/generateur.yaml
 */
final class GenerateurDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeGenerateur $type,
        public readonly ?TypeVmc $type_vmc,
        public readonly bool $generateur_collectif,
        public readonly ?bool $presence_echangeur_thermique,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
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
