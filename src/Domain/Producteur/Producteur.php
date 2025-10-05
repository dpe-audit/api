<?php

namespace App\Domain\Producteur;

final class Producteur
{
    public function __construct(
        public readonly string $siren,
        public readonly string $nom,
    ) {}
}
