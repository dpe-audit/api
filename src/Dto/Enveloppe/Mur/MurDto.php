<?php

namespace App\Dto\Enveloppe\Mur;

use App\Domain\Enveloppe\Mur\{Mur, TypeDoublage, TypeMur};
use App\Domain\Enveloppe\Paroi\Inertie;
use App\Dto\Enveloppe\Paroi\IsolationDto;

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
        public readonly ?int $annee_construction,
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
        public readonly PositionDto $position,
        public readonly IsolationDto $isolation,
    ) {}

    public static function from(Mur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type_structure: $data->type_structure(),
            epaisseur_structure: $data->epaisseur_structure(),
            type_doublage: $data->type_doublage(),
            presence_enduit_isolant: $data->presence_enduit_isolant(),
            paroi_ancienne: $data->paroi_ancienne(),
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
    }
}
