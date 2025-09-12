<?php

namespace App\Domain\Enveloppe\Porte\Menuiserie;

final class Menuiserie
{
    public function __construct(
        public readonly ?bool $presence_joint,
        public readonly ?bool $presence_retour_isolation,
        public readonly ?float $largeur_dormant,
    ) {}

    public static function create(
        ?bool $presence_joint = null,
        ?bool $presence_retour_isolation = null,
        ?float $largeur_dormant = null,
    ): self {
        return new self(
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
        );
    }
}
