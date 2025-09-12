<?php

namespace App\Domain\Refroidissement\Generateur;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Generateur>
 */
final class GenerateurCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Generateur
    {
        return array_find($this->elements, fn(Generateur $item): bool => $item->id()->compare($id));
    }
}
