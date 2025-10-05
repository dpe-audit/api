<?php

namespace App\Dto\Enveloppe\PlancherBas;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\PlancherBas\PlancherBas;
use App\Domain\Enveloppe\PlancherBas\PlancherBasCollection;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Dto\Enveloppe\Paroi\IsolationDto;

final class PlancherBasDto
{
    public function __construct(
        public string $id,
        public string $description,
        public ?TypePlancherBas $type_structure,
        public ?Inertie $inertie,
        public ?int $annee_construction,
        public ?int $annee_renovation,
        public ?float $u0,
        public ?float $u,
        public PositionDto $position,
        public IsolationDto $isolation,
    ) {}

    public static function from(PlancherBas $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
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

    /**
     * @return array<self>
     */
    public static function fromCollection(PlancherBasCollection $data): array
    {
        return $data->map(fn(PlancherBas $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
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
