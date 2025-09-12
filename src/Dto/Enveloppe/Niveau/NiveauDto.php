<?php

namespace App\Dto\Enveloppe\Niveau;

use App\Domain\Enveloppe\Niveau\InertieParoi;
use App\Domain\Enveloppe\Niveau\Niveau;
use App\Domain\Enveloppe\Niveau\NiveauCollection;

final class NiveauDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface,
        public InertieParoi $inertie_paroi_verticale,
        public InertieParoi $inertie_plancher_bas,
        public InertieParoi $inertie_plancher_haut,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(NiveauCollection $data): array
    {
        return $data->map(fn(Niveau $item) => self::from($item))->values();
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
