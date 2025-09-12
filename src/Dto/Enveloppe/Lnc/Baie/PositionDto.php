<?php

namespace App\Dto\Enveloppe\Lnc\Baie;

use App\Domain\Enveloppe\Lnc\Baie\Position;
use App\Domain\Enveloppe\Lnc\Baie\Mitoyennete;

final class PositionDto
{
    public function __construct(
        public float $surface,
        public Mitoyennete $mitoyennete,
        public ?float $orientation,
        public ?float $inclinaison,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
            orientation: $data->orientation,
            inclinaison: $data->inclinaison,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
            'orientation' => $this->orientation,
            'inclinaison' => $this->inclinaison,
        ];
    }
}
