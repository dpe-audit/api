<?php

namespace App\Domain\Chauffage\Installation\Solaire;

final class Solaire
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ?int $annee_installation,
        public readonly ?float $fch,
    ) {}

    public static function create(
        Usage $usage,
        ?int $annee_installation,
        ?float $fch,
    ): self {
        return new self(
            usage: $usage,
            annee_installation: $annee_installation,
            fch: $fch,
        );
    }
}
