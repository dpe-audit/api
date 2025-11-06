<?php

namespace App\Domain\Ventilation\Installation;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Installation>
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): static
    {
        return $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Installation
    {
        return array_find($this->elements, fn(Installation $item): bool => $item->id()->equals($id));
    }

    public function with_generateur(Id $generateur_id): static
    {
        return $this->filter(fn(Installation $item): bool => $item->generateur()?->id()->equals($generateur_id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry + $item->surface());
    }
}
