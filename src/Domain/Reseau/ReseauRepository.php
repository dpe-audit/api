<?php

namespace App\Domain\Reseau;

interface ReseauRepository
{
    public function find(string $id): ?Reseau;
}
