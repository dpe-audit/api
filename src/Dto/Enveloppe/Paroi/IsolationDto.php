<?php

namespace App\Dto\Enveloppe\Paroi;

use App\Domain\Enveloppe\Paroi\Isolation\{EtatIsolation, Isolation, TypeIsolation};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/paroi/isolation.yaml
 */
final class IsolationDto
{
    public function __construct(
        public readonly ?EtatIsolation $etat,
        public readonly ?TypeIsolation $type,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
        public readonly ?float $epaisseur,
        public readonly ?float $resistance_thermique,
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
            'etat' => $this->etat?->value,
            'type' => $this->type?->value,
            'annee_installation' => $this->annee_installation,
            'epaisseur' => $this->epaisseur,
            'resistance_thermique' => $this->resistance_thermique,
        ];
    }
}
