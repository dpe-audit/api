<?php

namespace App\Dto\Enveloppe\PlancherHaut;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherHaut\Position\Position;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/plancher_haut.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $orientation,
        public readonly ?string $local_non_chauffe_id,
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
