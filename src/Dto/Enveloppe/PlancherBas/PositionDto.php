<?php

namespace App\Dto\Enveloppe\PlancherBas;

use App\Domain\Enveloppe\PlancherBas\Position\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\Position\Position;

final class PositionDto
{
    public function __construct(
        public float $surface,
        public Mitoyennete $mitoyennete,
        public ?float $surface_ue,
        public ?float $perimetre_ue,
        public ?string $local_non_chauffe_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
            surface_ue: $data->surface_ue,
            perimetre_ue: $data->perimetre_ue,
            local_non_chauffe_id: $data->local_non_chauffe?->id() ? (string) $data->local_non_chauffe?->id() : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
            'surface_ue' => $this->surface_ue,
            'perimetre_ue' => $this->perimetre_ue,
            'local_non_chauffe_id' => $this->local_non_chauffe_id,
        ];
    }
}
