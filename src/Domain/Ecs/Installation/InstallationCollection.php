<?php

namespace App\Domain\Ecs\Installation;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Installation>
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Installation
    {
        return array_find($this->elements, fn(Installation $item) => $item->id()->equals($id));
    }

    public function with_generateur(Id $id): self
    {
        return $this->filter(fn(Installation $item): bool => $item->systemes()->with_generateur($id)->count() > 0);
    }

    public function with_systeme(Id $id): static
    {
        return $this->filter(fn(Installation $item): bool => null !== $item->systemes()->find($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->surface());
    }
}
