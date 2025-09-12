<?php

namespace App\Domain\Enveloppe\DoubleFenetre;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property DoubleFenetre[] $elements
 */
final class DoubleFenetreCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(DoubleFenetre $item) => $item->reinitialise());
    }

    public function find(Id $id): ?DoubleFenetre
    {
        return array_find($this->elements, fn(DoubleFenetre $item): bool => $item->id() === $id);
    }
}
