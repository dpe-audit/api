<?php

namespace App\Domain\Enveloppe\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\ParoiCollection;

/**
 * @property Porte[] $elements
 */
final class PorteCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Porte $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Porte
    {
        return array_find($this->elements, fn(Porte $item): bool => $item->id() === $id);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Porte $item) => $carry + $item->position()->surface);
    }
}
