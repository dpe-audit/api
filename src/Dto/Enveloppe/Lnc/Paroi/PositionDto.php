<?php

namespace App\Dto\Enveloppe\Lnc\Paroi;

use App\Domain\Enveloppe\Lnc\Paroi\Position;
use App\Domain\Enveloppe\Lnc\Paroi\Mitoyennete;

final class PositionDto
{
    public function __construct(
        public float $surface,
        public Mitoyennete $mitoyennete,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
        ];
    }
}
