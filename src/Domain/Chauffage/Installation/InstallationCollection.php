<?php

namespace App\Domain\Chauffage\Installation;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Installation>
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Installation
    {
        return array_find(
            $this->elements,
            fn(Installation $item): bool => $item->id()->compare($id),
        );
    }

    public function with_systeme(Id $id): static
    {
        return $this->filter(fn(Installation $item): bool => null !== $item->systemes()->find($id));
    }

    public function with_emetteur(Id $id): static
    {
        return $this->filter(fn(Installation $item): bool => $item->systemes()->has_emetteur($id));
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(fn(Installation $item): bool => $item->systemes()->has_emetteur($id));
    }

    public function has_generateur(Id $id): bool
    {
        return $this->with_generateur($id)->count() > 0;
    }

    public function with_effet_joule(): static
    {
        return $this->filter(fn(Installation $installation) => $installation->effet_joule());
    }

    public function effet_joule(): bool
    {
        return $this->with_effet_joule()->surface() > $this->surface() / 2;
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $surface, Installation $installation) => $surface + $installation->surface());
    }
}
