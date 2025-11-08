<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\{Generateur, EnergieGenerateur, TypeGenerateur};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/generateur.yaml
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
        public readonly PositionDto $position,
        public readonly SignaletiqueDto $signaletique,
    ) {}

    public static function from(Generateur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            energie: $data->energie(),
            annee_installation: $data->annee_installation(),
            position: PositionDto::from($data->position()),
            signaletique: SignaletiqueDto::from($data->signaletique()),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type?->value,
            'energie' => $this->energie?->value,
            'annee_installation' => $this->annee_installation,
            'position' => $this->position->__normalize(),
            'signaletique' => $this->signaletique->__normalize(),
        ];
    }
}
