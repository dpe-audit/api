<?php

namespace App\Domain\Enveloppe\Paroi;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Paroi>
 */
abstract class ParoiCollection extends ArrayCollection
{
    public function find(Id $id): ?Paroi
    {
        return array_find($this->elements, fn(Paroi $item): bool => $item->id() === $id);
    }
}
