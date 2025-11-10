<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, GenerateurData, TypeGenerateur, TypeVmc};
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
        public readonly ?GenerateurData $data = null,
    ) {}

    public static function from(Generateur $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: (string) $entity->description(),
            type: $entity->type(),
            type_vmc: $entity->type_vmc(),
            generateur_collectif: $entity->generateur_collectif(),
            presence_echangeur_thermique: $entity->presence_echangeur_thermique(),
            annee_installation: $entity->annee_installation(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data =  [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'type_vmc' => $this->type_vmc?->value,
            'generateur_collectif' => $this->generateur_collectif,
            'presence_echangeur_thermique' => $this->presence_echangeur_thermique,
            'annee_installation' => $this->annee_installation,
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'ratio_utilisation' => $this->data->ratio_utilisation,
                'pvent_moy' => $this->data->pvent_moy,
                'cef_aux' => $this->data->cef_aux,
                'cep_aux' => $this->data->cep_aux,
                'eges_aux' => $this->data->eges_aux,
            ];
        }
        return $data;
    }
}
