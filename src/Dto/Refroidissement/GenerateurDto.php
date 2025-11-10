<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Generateur\{Generateur, EnergieGenerateur, GenerateurData, TypeGenerateur};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/generateur.yaml
 */
final class GenerateurDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
        public readonly ?float $seer,
        public readonly ?string $reseau_froid_id,
        public readonly ?GenerateurData $data = null,
    ) {}

    public static function from(Generateur $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: (string) $entity->description(),
            type: $entity->type(),
            energie: $entity->energie(),
            annee_installation: $entity->annee_installation(),
            seer: $entity->seer(),
            reseau_froid_id: $entity->reseau_froid() ? (string) $entity->reseau_froid()->id() : null,
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'energie' => $this->energie->value,
            'annee_installation' => $this->annee_installation,
            'seer' => $this->seer,
            'reseau_froid_id' => $this->reseau_froid_id,
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'eer' => $this->data->eer,
                'consommations' => $this->data->consommations?->__normalize(),
            ];
        }
        return $data;
    }
}
