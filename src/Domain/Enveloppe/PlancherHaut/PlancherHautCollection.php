<?php

namespace App\Domain\Enveloppe\PlancherHaut;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\ParoiCollection;

/**
 * @property PlancherHaut[] $elements
 */
final class PlancherHautCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PlancherHaut $item) => $item->reinitialise());
    }

    public function find(Id $id): ?PlancherHaut
    {
        return array_find($this->elements, fn(PlancherHaut $item): bool => $item->id()->equals($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, PlancherHaut $item) => $carry + $item->position()->surface);
    }
}
