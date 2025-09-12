<?php

namespace App\Dto\Enveloppe\PlancherHaut;

use App\Domain\Enveloppe\PlancherHaut\Configuration;
use App\Domain\Enveloppe\PlancherHaut\Inertie;
use App\Domain\Enveloppe\PlancherHaut\PlancherHaut;
use App\Domain\Enveloppe\PlancherHaut\PlancherHautCollection;
use App\Domain\Enveloppe\PlancherHaut\TypePlancherHaut;

final class PlancherHautDto
{
    public function __construct(
        public string $id,
        public string $description,
        public Configuration $configuration,
        public ?TypePlancherHaut $type_structure,
        public ?Inertie $inertie,
        public ?int $annee_construction,
        public ?int $annee_renovation,
        public ?float $u0,
        public ?float $u,
        public PositionDto $position,
        public IsolationDto $isolation,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(PlancherHautCollection $data): array
    {
        return $data->map(fn(PlancherHaut $item) => self::from($item))->values();
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
