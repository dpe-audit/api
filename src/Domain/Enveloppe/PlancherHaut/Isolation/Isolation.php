<?php

namespace App\Domain\Enveloppe\PlancherHaut\Isolation;

final class Isolation
{
    public function __construct(
        public readonly ?EtatIsolation $etat = null,
        public readonly ?TypeIsolation $type = null,
        public readonly ?int $annee_installation = null,
        public readonly ?float $epaisseur = null,
        public readonly ?float $resistance_thermique = null,
    ) {}

    public static function create(
        ?EtatIsolation $etat,
        ?TypeIsolation $type = null,
        ?int $annee_installation = null,
        ?float $epaisseur = null,
        ?float $resistance_thermique = null,
    ): self {
        return new self(
            etat: $etat,
            type: $type,
            annee_installation: $annee_installation,
            epaisseur: $epaisseur,
            resistance_thermique: $resistance_thermique,
        );
    }
}
