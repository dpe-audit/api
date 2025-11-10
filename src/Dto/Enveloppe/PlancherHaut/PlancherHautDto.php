<?php

namespace App\Dto\Enveloppe\PlancherHaut;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\PlancherHaut\{Configuration, PlancherHaut, PlancherHautData, TypePlancherHaut};
use App\Dto\Enveloppe\Paroi\IsolationDto;
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/plancher_haut.yaml
 */
final class PlancherHautDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly Configuration $configuration,
        public readonly ?TypePlancherHaut $type_structure,
        public readonly ?Inertie $inertie,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_construction,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
        public readonly PositionDto $position,
        public readonly IsolationDto $isolation,

        public readonly ?PlancherHautData $data = null,
    ) {}

    public static function from(PlancherHaut $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            configuration: $entity->configuration(),
            type_structure: $entity->type_structure(),
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
            'configuration' => $this->configuration->value,
            'type_structure' => $this->type_structure?->value,
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
