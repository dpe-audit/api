<?php

namespace App\Dto\Enveloppe\Niveau;

use App\Domain\Enveloppe\Niveau\Niveau;
use App\Domain\Enveloppe\Paroi\Inertie;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/niveau.yaml
 */
final class NiveauDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly Inertie $inertie_paroi_verticale,
        public readonly Inertie $inertie_plancher_bas,
        public readonly Inertie $inertie_plancher_haut,
    ) {}

    public static function from(Niveau $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
            inertie_paroi_verticale: $data->inertie_paroi_verticale(),
            inertie_plancher_bas: $data->inertie_plancher_bas(),
            inertie_plancher_haut: $data->inertie_plancher_haut(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'inertie_paroi_verticale' => $this->inertie_paroi_verticale->value,
            'inertie_plancher_bas' => $this->inertie_plancher_bas->value,
            'inertie_plancher_haut' => $this->inertie_plancher_haut->value,
        ];
    }
}
