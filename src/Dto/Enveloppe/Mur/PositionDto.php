<?php

namespace App\Dto\Enveloppe\Mur;

use App\Domain\Enveloppe\Mur\Position\Mitoyennete;
use App\Domain\Enveloppe\Mur\Position\Position;

final class PositionDto
{
    public function __construct(
        public float $surface,
        public Mitoyennete $mitoyennete,
        public ?float $orientation,
        public ?string $local_non_chauffe_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
            orientation: $data->orientation,
            local_non_chauffe_id: $data->local_non_chauffe?->id() ? (string) $data->local_non_chauffe?->id() : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
            'orientation' => $this->orientation,
            'local_non_chauffe_id' => $this->local_non_chauffe_id,
        ];
    }
}
