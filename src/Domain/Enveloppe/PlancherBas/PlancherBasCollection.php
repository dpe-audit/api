<?php

namespace App\Domain\Enveloppe\PlancherBas;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\ParoiCollection;

/**
 * @property PlancherBas[] $elements
 */
final class PlancherBasCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PlancherBas $item) => $item->reinitialise());
    }

    public function find(Id $id): ?PlancherBas
    {
        return array_find($this->elements, fn(PlancherBas $item): bool => $item->id()->equals($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, PlancherBas $item) => $carry + $item->position()->surface);
    }
}
