<?php

namespace App\Domain\Enveloppe\Masque;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Masque>
 */
final class MasqueCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Masque $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Masque
    {
        return array_find($this->elements, fn(Masque $item): bool => $item->id()->compare($id));
    }

    public function with_type(TypeMasque $type): static
    {
        return $this->filter(fn(Masque $item): bool => $item->type() === $type);
    }
}
