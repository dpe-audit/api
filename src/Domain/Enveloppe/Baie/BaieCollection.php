<?php

namespace App\Domain\Enveloppe\Baie;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\ParoiCollection;

/**
 * @property Baie[] $elements
 */
final class BaieCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Baie $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Baie
    {
        return array_find($this->elements, fn(Baie $item): bool => $item->id() === $id);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Baie $item) => $carry + $item->position()->surface);
    }
}
