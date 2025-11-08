<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Generateur\{Generateur, EnergieGenerateur, TypeGenerateur};
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
    ) {}

    public static function from(Generateur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: (string) $data->description(),
            type: $data->type(),
            energie: $data->energie(),
            annee_installation: $data->annee_installation(),
            seer: $data->seer(),
            reseau_froid_id: $data->reseau_froid() ? (string) $data->reseau_froid()->id() : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'energie' => $this->energie->value,
            'annee_installation' => $this->annee_installation,
            'seer' => $this->seer,
            'reseau_froid_id' => $this->reseau_froid_id,
        ];
    }
}
