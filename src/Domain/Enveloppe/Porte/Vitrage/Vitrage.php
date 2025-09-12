<?php

namespace App\Domain\Enveloppe\Porte\Vitrage;

final class Vitrage
{
    public function __construct(
        public readonly float $surface,
        public readonly ?TypeVitrage $type,
    ) {}

    public static function create(float $surface, ?TypeVitrage $type): self
    {
        return new self(
            surface: $surface,
            type: $type,
        );
    }
}
