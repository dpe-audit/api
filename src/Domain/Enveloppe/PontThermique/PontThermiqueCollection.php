<?php

namespace App\Domain\Enveloppe\PontThermique;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<PontThermique>
 */
final class PontThermiqueCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PontThermique $item) => $item->reinitialise());
    }

    public function find(Id $id): ?PontThermique
    {
        return array_find($this->elements, fn(PontThermique $item): bool => $item->id()->equals($id));
    }
}
