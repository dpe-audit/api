<?php

namespace App\Domain\Ecs\Generateur;

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
        return array_find($this->elements, fn(Generateur $item) => $item->id()->compare($id));
    }

    public function with_installation(Id $id): self
    {
        return $this->filter(fn(Generateur $item): bool => $item->ecs()->systemes()
            ->with_generateur($item->id())
            ->has_installation($id));
    }

    public function with_systeme(Id $id): self
    {
        return $this->filter(
            fn(Generateur $item): bool => $item->ecs()->systemes()->has_generateur($id)
        );
    }
}
