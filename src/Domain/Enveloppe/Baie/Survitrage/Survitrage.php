<?php

namespace App\Domain\Enveloppe\Baie\Survitrage;

final class Survitrage
{
    public function __construct(
        public readonly ?TypeSurvitrage $type,
        public readonly ?int $epaisseur_lame,
    ) {}

    public static function create(
        ?TypeSurvitrage $type,
        ?int $epaisseur_lame,
    ): self {
        return new self(type: $type, epaisseur_lame: $epaisseur_lame);
    }
}
