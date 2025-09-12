<?php

namespace App\Domain\Enveloppe\DoubleFenetre\Menuiserie;

final class Menuiserie
{
    public function __construct(
        public readonly ?Materiau $materiau,
        public readonly ?float $largeur_dormant,
        public readonly ?bool $presence_joint,
        public readonly ?bool $presence_retour_isolation,
        public readonly ?bool $presence_rupteur_pont_thermique,
    ) {}

    public static function create(
        ?Materiau $materiau = null,
        ?float $largeur_dormant = null,
        ?bool $presence_joint = null,
        ?bool $presence_retour_isolation = null,
        ?bool $presence_rupteur_pont_thermique = null,
    ): self {
        return new self(
            materiau: $materiau,
            largeur_dormant: $largeur_dormant,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        );
    }
}
