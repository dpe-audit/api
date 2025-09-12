<?php

namespace App\Api\Ressource\Collection;

use App\Domain\Ressource\Ressource;
use App\Dto\Adresse\AdresseDto;
use App\Dto\Batiment\BatimentDto;

final class RessourceItem
{
    public function __construct(
        public string $id,

        public string $date_etablissement,

        public AdresseDto $adresse,

        public BatimentDto $batiment,

    ) {}

    public static function from(Ressource $entity): self
    {
        return new self(
            id: $entity->id(),
            date_etablissement: $entity->date_etablissement()->format('Y-m-d'),
            adresse: AdresseDto::from($entity->adresse()),
            batiment: BatimentDto::from($entity->batiment()),
        );
    }
}
