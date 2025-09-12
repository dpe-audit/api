<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Generateur\EnergieGenerateur;
use App\Domain\Refroidissement\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Refroidissement\Generateur\TypeGenerateur;

final class GenerateurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeGenerateur $type,
        public EnergieGenerateur $energie,
        public ?int $annee_installation,
        public ?float $seer,
        public ?string $reseau_froid_id,
    ) {}

    public static function from(Generateur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: (string) $data->description(),
            type: $data->type(),
            energie: $data->energie(),
            annee_installation: $data->annee_installation(),
            seer: $data->seer(),
            reseau_froid_id: $data->reseau_froid() ? (string) $data->reseau_froid()->id() : null,
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
            'type' => $this->type->value,
            'energie' => $this->energie->value,
            'annee_installation' => $this->annee_installation,
            'seer' => $this->seer,
            'reseau_froid_id' => $this->reseau_froid_id,
        ];
    }
}
