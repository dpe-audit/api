<?php

namespace App\Domain\Enveloppe\Baie\Vitrage;

final class Vitrage
{
    public function __construct(
        public readonly TypeVitrage $type,
        public readonly ?NatureGazLame $nature_lame,
        public readonly ?int $epaisseur_lame,
    ) {}

    public static function create(
        TypeVitrage $type,
        ?NatureGazLame $nature_lame,
        ?int $epaisseur_lame,
    ): self {
        return new self(
            type: $type,
            nature_lame: $nature_lame,
            epaisseur_lame: $epaisseur_lame
        );
    }
}
