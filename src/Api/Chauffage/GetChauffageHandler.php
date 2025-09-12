<?php

namespace App\Api\Chauffage;

use App\Model\Chauffage\{Chauffage, ChauffageRepository};
use App\Model\Common\ValueObject\Id;

final class GetChauffageHandler
{
    public function __construct(private readonly ChauffageRepository $repository) {}

    public function __invoke(Id $id): ?Chauffage
    {
        return $this->repository->find($id);
    }
}
