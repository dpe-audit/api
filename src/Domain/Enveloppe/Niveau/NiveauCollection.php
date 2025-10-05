<?php

namespace App\Domain\Enveloppe\Niveau;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Niveau>
 */
final class NiveauCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Niveau $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Niveau
    {
        return array_find($this->elements, fn(Niveau $item): bool => $item->id()->equals($id));
    }
}
