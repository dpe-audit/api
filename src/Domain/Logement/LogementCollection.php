<?php

namespace App\Domain\Logement;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

final class LogementCollection extends ArrayCollection
{
    public function find(Id $id): ?Logement
    {
        return array_find($this->elements, fn(Logement $item): bool => $item->id()->compare($id));
    }
}
