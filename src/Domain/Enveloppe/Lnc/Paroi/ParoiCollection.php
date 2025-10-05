<?php

namespace App\Domain\Enveloppe\Lnc\Paroi;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Paroi>
 */
final class ParoiCollection extends ArrayCollection
{
    public function reinitialise(): static
    {
        return $this->walk(fn(Paroi $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Paroi
    {
        return array_find($this->elements, fn(Paroi $item): bool => $item->id()->equals($id));
    }

    public function with_mitoyennetes(Mitoyennete ...$mitoyennetes): self
    {
        return $this->filter(
            fn(Paroi $item): bool => in_array($item->position()->mitoyennete, $mitoyennetes)
        );
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, Paroi $item): float => $surface + $item->position()->surface
        );
    }
}
