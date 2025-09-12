<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Generateur\GenerateurCollection;
use App\Domain\Ecs\Generateur\TypeGenerateur;

final class GenerateurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeGenerateur $type,
        public EnergieGenerateur $energie,
        public ?int $annee_installation,
        public PositionDto $position,
        public SignaletiqueDto $signaletique,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(GenerateurCollection $data): array
    {
        return $data->map(fn(Generateur $item) => self::from($item))->values();
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
