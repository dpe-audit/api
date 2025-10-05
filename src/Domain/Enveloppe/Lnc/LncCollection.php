<?php

namespace App\Domain\Enveloppe\Lnc;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Lnc>
 */
final class LncCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Lnc $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Lnc
    {
        return array_find($this->elements, fn(Lnc $item): bool => $item->id()->equals($id));
    }

    public function with_types(TypeLnc ...$types): self
    {
        return $this->filter(fn(Lnc $item): bool => \in_array($item->type(), $types));
    }
}
