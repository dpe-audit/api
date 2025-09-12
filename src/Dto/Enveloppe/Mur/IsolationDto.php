<?php

namespace App\Dto\Enveloppe\Mur;

use App\Domain\Enveloppe\Mur\Isolation\EtatIsolation;
use App\Domain\Enveloppe\Mur\Isolation\Isolation;
use App\Domain\Enveloppe\Mur\Isolation\TypeIsolation;

final class IsolationDto
{
    public function __construct(
        public EtatIsolation $etat,
        public ?TypeIsolation $type,
        public ?int $annee_installation,
        public ?float $epaisseur,
        public ?float $resistance_thermique,
    ) {}

    public static function from(Isolation $data): self
    {
        return new self(
            etat: $data->etat,
            type: $data->type,
            annee_installation: $data->annee_installation,
            epaisseur: $data->epaisseur,
            resistance_thermique: $data->resistance_thermique,
        );
    }

    public function __normalize(): array
    {
        return [
            'etat' => $this->etat->value,
            'type' => $this->type?->value,
            'annee_installation' => $this->annee_installation,
            'epaisseur' => $this->epaisseur,
            'resistance_thermique' => $this->resistance_thermique,
        ];
    }
}
