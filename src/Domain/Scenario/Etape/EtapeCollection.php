<?php

namespace App\Domain\Scenario\Etape;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

final class EtapeCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Etape $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Etape
    {
        return array_find($this->elements, fn(Etape $item): bool => $item->id()->equals($id));
    }
}
