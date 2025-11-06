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
        return array_find($this->elements, fn(Baie $item): bool => $item->id()->equals($id));
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
            fn(Baie $item): bool => $item->position()->orientation?->equals($orientation) ?? false
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

    /**
     * Orientations majoritaires des baies
     * 
     * @return Orientation[]
     */
    public function orientations(): array
    {
        /** @var array<string, float> */
        $orientations = [];

        foreach (Orientation::cases() as $orientation) {
            $orientations[$orientation->value] = array_reduce(
                array_filter($this->elements, fn($item) => $item->position()->orientation() === $orientation),
                fn(float $carry, $item): float => $carry += $item->position()->surface,
                0
            );
        }
        $max = max($orientations);
        $keys = array_keys($orientations, $max);
        return array_map(fn($key) => Orientation::from($key), $keys);
    }
}
