<?php

namespace App\Domain\Enveloppe\Mur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\ParoiCollection;

/**
 * @property Mur[] $elements
 */
final class MurCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Mur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Mur
    {
        return array_find($this->elements, fn(Mur $item): bool => $item->id()->equals($id));
    }

    public function with_paroi_ancienne(bool $paroi_ancienne): static
    {
        return $this->filter(fn(Mur $item) => $item->paroi_ancienne() === $paroi_ancienne,);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Mur $item) => $carry + $item->position()->surface);
    }
}
