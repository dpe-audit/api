<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Menuiserie;

final class MenuiserieDto
{
    public function __construct(
        public ?Materiau $materiau,
        public ?float $largeur_dormant,
        public ?bool $presence_joint,
        public ?bool $presence_retour_isolation,
        public ?bool $presence_rupteur_pont_thermique,
    ) {}

    public static function from(Menuiserie $data): self
    {
        return new self(
            materiau: $data->materiau,
            largeur_dormant: $data->largeur_dormant,
            presence_joint: $data->presence_joint,
            presence_retour_isolation: $data->presence_retour_isolation,
            presence_rupteur_pont_thermique: $data->presence_rupteur_pont_thermique,
        );
    }

    public function __normalize(): array
    {
        return [
            'materiau' => $this->materiau?->value,
            'largeur_dormant' => $this->largeur_dormant,
            'presence_joint' => $this->presence_joint,
            'presence_retour_isolation' => $this->presence_retour_isolation,
            'presence_rupteur_pont_thermique' => $this->presence_rupteur_pont_thermique,
        ];
    }
}
