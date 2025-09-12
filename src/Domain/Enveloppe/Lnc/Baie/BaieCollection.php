<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Baie>
 */
final class BaieCollection extends ArrayCollection
{
    public function reinitialise(): static
    {
        return $this->walk(fn(Baie $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Baie
    {
        return array_find($this->elements, fn(Baie $item): bool => $item->id()->compare($id));
    }

    public function with_mitoyennetes(Mitoyennete ...$mitoyennete): self
    {
        return $this->filter(
            fn(Baie $item): bool => in_array($item->position()->mitoyennete, $mitoyennete)
        );
    }

    public function with_orientation(Orientation $orientation): self
    {
        return $this->filter(
            fn(Baie $item): bool => $item->position()->orientation?->compare($orientation) ?? false
        );
    }

    public function with_inclinaison(bool $est_verticale): self
    {
        return $this->filter(
            fn(Baie $item): bool => $item->position()->inclinaison >= 75 === $est_verticale
        );
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, Baie $item): float => $surface + $item->position()->surface
        );
    }
}
