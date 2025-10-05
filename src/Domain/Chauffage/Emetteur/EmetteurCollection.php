<?php

namespace App\Domain\Chauffage\Emetteur;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Emetteur>
 */
final class EmetteurCollection extends ArrayCollection
{
    public function find(Id $id): ?Emetteur
    {
        return array_find($this->elements, fn(Emetteur $item): bool => $item->id()->equals($id));
    }

    public function with_installation(Id $id): self
    {
        return $this->filter(fn(Emetteur $item): bool => $item->chauffage()->systemes()
            ->with_emetteur($item->id())
            ->has_installation($id));
    }

    public function with_systeme(Id $id): self
    {
        return $this->filter(
            fn(Emetteur $item): bool => $item->chauffage()->systemes()->has_emetteur($id)
        );
    }

    public function with_generateur(Id $id): self
    {
        return $this->filter(fn(Emetteur $item): bool => $item->chauffage()->systemes()
            ->with_emetteur($item->id())
            ->has_generateur($id));
    }
}
