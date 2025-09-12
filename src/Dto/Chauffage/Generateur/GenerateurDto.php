<?php

namespace App\Dto\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Chauffage\Generateur\GenerateurCollection;
use App\Domain\Chauffage\Generateur\TypeChaudiere;
use App\Domain\Chauffage\Generateur\TypeGenerateur;

final class GenerateurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public ?TypeGenerateur $type,
        public ?EnergieGenerateur $energie,
        public ?EnergieGenerateur $bienergie,
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
            bienergie: $data->bienergie(),
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
            'bienergie' => $this->bienergie?->value,
            'annee_installation' => $this->annee_installation,
            'position' => $this->position->__normalize(),
            'signaletique' => $this->signaletique->__normalize(),
        ];
    }
}
