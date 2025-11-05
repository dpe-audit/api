<?php

namespace App\Dto\Enveloppe\Lnc\Paroi;

use App\Domain\Enveloppe\Lnc\Paroi\{Position, Mitoyennete};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/local_non_chauffe/paroi.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
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
