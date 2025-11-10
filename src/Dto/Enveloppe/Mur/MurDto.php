<?php

namespace App\Dto\Enveloppe\Mur;

use App\Domain\Enveloppe\Mur\{Mur, MurData, TypeDoublage, TypeMur};
use App\Domain\Enveloppe\Paroi\Inertie;
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/mur.yaml
 */
final class MurDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?TypeMur $type_structure,
        public readonly ?float $epaisseur_structure,
        public readonly ?TypeDoublage $type_doublage,
        public readonly ?bool $presence_enduit_isolant,
        public readonly ?bool $paroi_ancienne,
        public readonly ?Inertie $inertie,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_construction,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
        public readonly PositionDto $position,
        public readonly IsolationDto $isolation,
        public readonly ?MurData $data = null,
    ) {}

    public static function from(Mur $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type_structure: $entity->type_structure(),
            epaisseur_structure: $entity->epaisseur_structure(),
            type_doublage: $entity->type_doublage(),
            presence_enduit_isolant: $entity->presence_enduit_isolant(),
            paroi_ancienne: $entity->paroi_ancienne(),
            inertie: $entity->inertie(),
            annee_construction: $entity->annee_construction(),
            annee_renovation: $entity->annee_renovation(),
            u0: $entity->u0(),
            u: $entity->u(),
            position: PositionDto::from($entity->position()),
            isolation: IsolationDto::from($entity->isolation()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'type_structure' => $this->type_structure?->value,
            'epaisseur_structure' => $this->epaisseur_structure,
            'type_doublage' => $this->type_doublage?->value,
            'presence_enduit_isolant' => $this->presence_enduit_isolant,
            'paroi_ancienne' => $this->paroi_ancienne,
            'inertie' => $this->inertie?->value,
            'annee_construction' => $this->annee_construction,
            'annee_renovation' => $this->annee_renovation,
            'u0' => $this->u0,
            'u' => $this->u,
            'position' => $this->position->__normalize(),
            'isolation' => $this->isolation->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'sdep' => $this->data->sdep,
                'u0' => $this->data->u0,
                'u' => $this->data->u,
                'b' => $this->data->b,
                'dp' => $this->data->dp,
                'performance' => $this->data->performance?->value,
            ];
        }
        return $data;
    }
}
