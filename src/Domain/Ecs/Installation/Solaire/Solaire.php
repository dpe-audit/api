<?php

namespace App\Domain\Ecs\Installation\Solaire;

final class Solaire
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ?int $annee_installation,
        public readonly ?float $fecs,
    ) {}

    public static function create(
        Usage $usage,
        ?int $annee_installation,
        ?float $fecs,
    ): self {
        return new self(
            usage: $usage,
            annee_installation: $annee_installation,
            fecs: $fecs,
        );
    }
}
