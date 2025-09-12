<?php

namespace App\Domain\Ecs\Systeme\Reseau;

final class Reseau
{
    public function __construct(
        public readonly bool $alimentation_contigue,
        public readonly int $niveaux_desservis,
        public readonly ?IsolationReseau $isolation,
        public readonly ?BouclageReseau $bouclage,
    ) {}

    public static function create(
        bool $alimentation_contigue,
        int $niveaux_desservis,
        ?IsolationReseau $isolation,
        ?BouclageReseau $bouclage,
    ): self {
        return new self(
            alimentation_contigue: $alimentation_contigue,
            niveaux_desservis: $niveaux_desservis,
            isolation: $isolation,
            bouclage: $bouclage,
        );
    }
}
