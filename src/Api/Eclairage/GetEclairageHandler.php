<?php

namespace App\Api\Eclairage;

use App\Model\Eclairage\{Eclairage, EclairageRepository};
use App\Model\Common\ValueObject\Id;

final class GetEclairageHandler
{
    public function __construct(private readonly EclairageRepository $repository) {}

    public function __invoke(Id $id): ?Eclairage
    {
        return $this->repository->find($id);
    }
}
