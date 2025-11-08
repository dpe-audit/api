<?php

namespace App\Domain\Ecs\Systeme;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Systeme>
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Systeme
    {
        return array_find($this->elements, fn(Systeme $item) => $item->id()->equals($id));
    }

    public function with_installation(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->installation()->id()->equals($id));
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->id()->equals($id));
    }

    public function has_installation(Id $id): bool
    {
        return $this->with_installation($id)->count() > 0;
    }

    public function has_generateur(Id $id): bool
    {
        return $this->with_generateur($id)->count() > 0;
    }

    public function volume_stockage(): float
    {
        return $this->reduce(fn(float $vs, Systeme $item) => $vs + $item->stockage()->volume);
    }
}
