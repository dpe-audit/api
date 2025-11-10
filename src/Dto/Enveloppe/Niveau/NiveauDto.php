<?php

namespace App\Dto\Enveloppe\Niveau;

use App\Domain\Enveloppe\Niveau\{Niveau, NiveauData};
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
        public readonly ?NiveauData $data = null,
    ) {}

    public static function from(Niveau $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            inertie_paroi_verticale: $entity->inertie_paroi_verticale(),
            inertie_plancher_bas: $entity->inertie_plancher_bas(),
            inertie_plancher_haut: $entity->inertie_plancher_haut(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'inertie_paroi_verticale' => $this->inertie_paroi_verticale->value,
            'inertie_plancher_bas' => $this->inertie_plancher_bas->value,
            'inertie_plancher_haut' => $this->inertie_plancher_haut->value,
        ];
        if ($this->data) {
            $data['data'] = [
                'inertie' => $this->data->inertie?->value,
            ];
        }
        return $data;
    }
}
