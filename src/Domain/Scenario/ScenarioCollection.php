<?php

namespace App\Domain\Scenario;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

final class ScenarioCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Scenario $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Scenario
    {
        return array_find($this->elements, fn(Scenario $item): bool => $item->id()->equals($id));
    }
}
