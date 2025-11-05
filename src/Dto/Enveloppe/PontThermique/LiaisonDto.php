<?php

namespace App\Dto\Enveloppe\PontThermique;

use App\Domain\Enveloppe\PontThermique\Liaison\{Liaison, TypeLiaison};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/pont_thermique.yaml
 */
final class LiaisonDto
{
    public function __construct(
        public readonly TypeLiaison $type,
        public readonly bool $pont_thermique_partiel,
        public readonly string $mur_id,
        public readonly ?string $plancher_id,
        public readonly ?string $ouverture_id,
    ) {}

    public static function from(Liaison $data): self
    {
        return new self(
            type: $data->type,
            pont_thermique_partiel: $data->pont_thermique_partiel,
            mur_id: (string) $data->mur->id(),
            plancher_id: (string) $data->plancher->id(),
            ouverture_id: (string) $data->ouverture->id(),
        );
    }

    public function __normalize(): array
    {
        return [
            'type' => $this->type->value,
            'pont_thermique_partiel' => $this->pont_thermique_partiel,
            'mur_id' => $this->mur_id,
            'plancher_id' => $this->plancher_id,
            'ouverture_id' => $this->ouverture_id,
        ];
    }
}
