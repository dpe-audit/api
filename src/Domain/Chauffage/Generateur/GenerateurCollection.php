<?php

namespace App\Domain\Chauffage\Generateur;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Generateur>
 */
final class GenerateurCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Generateur
    {
        return array_find($this->elements, fn(Generateur $item): bool => $item->id()->equals($id));
    }

    public function with_installation(Id $id): self
    {
        return $this->filter(fn(Generateur $item): bool => $item->chauffage()->systemes()
            ->with_generateur($item->id())
            ->has_installation($id));
    }

    public function with_systeme(Id $id): self
    {
        return $this->filter(
            fn(Generateur $item): bool => $item->chauffage()->systemes()->has_generateur($id)
        );
    }

    public function with_emetteur(Id $id): self
    {
        return $this->filter(fn(Generateur $item): bool => $item->chauffage()->systemes()
            ->with_generateur($item->id())
            ->has_emetteur($id));
    }

    public function with_type(TypeGenerateur $type): self
    {
        return $this->filter(fn(Generateur $generateur) => $generateur->type() === $type);
    }
}
