<?php

namespace App\Domain\Chauffage\Systeme\Reseau;

final class Reseau
{
    public function __construct(
        public readonly TypeDistribution $type_distribution,
        public readonly bool $presence_fluide_frigorigene,
        public readonly bool $presence_circulateur_externe,
        public readonly int $niveaux_desservis,
        public readonly ?IsolationReseau $isolation,
    ) {}

    public static function create(
        TypeDistribution $type_distribution,
        bool $presence_fluide_frigorigene,
        bool $presence_circulateur_externe,
        int $niveaux_desservis,
        ?IsolationReseau $isolation,
    ): self {
        return new self(
            type_distribution: $type_distribution,
            presence_fluide_frigorigene: $presence_fluide_frigorigene,
            presence_circulateur_externe: $presence_circulateur_externe,
            niveaux_desservis: $niveaux_desservis,
            isolation: $isolation,
        );
    }
}
