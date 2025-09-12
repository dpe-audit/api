<?php

namespace App\Api\Refroidissement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Generateur\Generateur;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Reseau\ReseauRepository;
use App\Dto\Refroidissement\GenerateurDto;

final class CreateGenerateurHandler
{
    public function __construct(private ReseauRepository $reseau_repository) {}

    public function __invoke(GenerateurDto $payload, Refroidissement $entity): Generateur
    {
        $reseau = $payload->reseau_froid_id
            ? $this->reseau_repository->find($payload->reseau_froid_id)
            : null;

        return Generateur::create(
            id: Id::fromString($payload->id),
            refroidissement: $entity,
            description: $payload->description,
            type: $payload->type,
            energie: $payload->energie,
            annee_installation: $payload->annee_installation,
            seer: $payload->seer,
            reseau_froid: $reseau,
        );
    }
}
