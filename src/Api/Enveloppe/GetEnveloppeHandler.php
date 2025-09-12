<?php

namespace App\Api\Enveloppe;

use App\Model\Enveloppe\{Enveloppe, EnveloppeRepository};
use App\Model\Common\ValueObject\Id;

final class GetEnveloppeHandler
{
    public function __construct(private readonly EnveloppeRepository $repository) {}

    public function __invoke(Id $id): ?Enveloppe
    {
        return $this->repository->find($id);
    }
}
