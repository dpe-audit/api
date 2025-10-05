<?php

namespace App\Dto\Enveloppe\Mur;

use App\Domain\Enveloppe\Mur\Mur;
use App\Domain\Enveloppe\Mur\MurCollection;
use App\Domain\Enveloppe\Mur\TypeDoublage;
use App\Domain\Enveloppe\Mur\TypeMur;
use App\Domain\Enveloppe\Paroi\Inertie;
use App\Dto\Enveloppe\Paroi\IsolationDto;

final class MurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public ?TypeMur $type_structure,
        public ?float $epaisseur_structure,
        public ?TypeDoublage $type_doublage,
        public ?bool $presence_enduit_isolant,
        public ?bool $paroi_ancienne,
        public ?Inertie $inertie,
        public ?int $annee_construction,
        public ?int $annee_renovation,
        public ?float $u0,
        public ?float $u,
        public PositionDto $position,
        public IsolationDto $isolation,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(MurCollection $data): array
    {
        return $data->map(fn(Mur $item) => self::from($item))->values();
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
