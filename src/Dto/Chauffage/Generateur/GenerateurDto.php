<?php

namespace App\Dto\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\{Generateur, EnergieGenerateur, GenerateurData, TypeGenerateur};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/generateur.yaml
 */
final class GenerateurDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?TypeGenerateur $type,
        public readonly ?EnergieGenerateur $energie,
        public readonly ?EnergieGenerateur $bienergie,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
        public readonly PositionDto $position,
        public readonly SignaletiqueDto $signaletique,
        public readonly ?GenerateurData $data = null,
    ) {}

    public static function from(Generateur $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            energie: $entity->energie(),
            bienergie: $entity->bienergie(),
            annee_installation: $entity->annee_installation(),
            position: PositionDto::from($entity->position()),
            signaletique: SignaletiqueDto::from($entity->signaletique()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type?->value,
            'energie' => $this->energie?->value,
            'bienergie' => $this->bienergie?->value,
            'annee_installation' => $this->annee_installation,
            'position' => $this->position->__normalize(),
            'signaletique' => $this->signaletique->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'pn' => $this->data->pn,
                'pdim' => $this->data->pdim,
                'pch' => $this->data->pch,
                'scop' => $this->data->scop,
                'rpn' => $this->data->rpn,
                'rpint' => $this->data->rpint,
                'qp0' => $this->data->qp0,
                'pveilleuse' => $this->data->pveilleuse,
                'tfonc30' => $this->data->tfonc30,
                'tfonc100' => $this->data->tfonc100,
                'pertes_generation' => $this->data->pertes_generation,
                'pertes_generation_recuperables' => $this->data->pertes_generation_recuperables,
            ];
        }
        return $data;
    }
}
