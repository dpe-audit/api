<?php

namespace App\Api\Ecs;

use App\Model\Ecs\{Ecs, EcsRepository};
use App\Model\Common\ValueObject\Id;

final class GetEcsHandler
{
    public function __construct(private readonly EcsRepository $repository) {}

    public function __invoke(Id $id): ?Ecs
    {
        return $this->repository->find($id);
    }
}
