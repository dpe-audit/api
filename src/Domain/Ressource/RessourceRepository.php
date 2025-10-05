<?php

namespace App\Domain\Ressource;

use App\Domain\Common\ValueObject\Id;

interface RessourceRepository
{
    public function save(Ressource $simulation): void;

    public function find(Id $id): ?Ressource;

    public function remove(Ressource $simulation): void;
}
