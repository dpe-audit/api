<?php

namespace App\Dto\Enveloppe\Lnc\Baie;

use App\Domain\Enveloppe\Lnc\Baie\{Position, Mitoyennete};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/local_non_chauffe/baie.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $orientation,
        public readonly ?float $inclinaison,
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
