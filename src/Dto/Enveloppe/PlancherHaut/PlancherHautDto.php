<?php

namespace App\Dto\Enveloppe\PlancherHaut;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\PlancherHaut\{Configuration, PlancherHaut, TypePlancherHaut};
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
    ) {}

    public static function from(PlancherHaut $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            configuration: $data->configuration(),
            type_structure: $data->type_structure(),
            inertie: $data->inertie(),
            annee_construction: $data->annee_construction(),
            annee_renovation: $data->annee_renovation(),
            u0: $data->u0(),
            u: $data->u(),
            position: PositionDto::from($data->position()),
            isolation: IsolationDto::from($data->isolation()),
        );
    }

    public function __normalize(): array
    {
        return [
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
    }
}
