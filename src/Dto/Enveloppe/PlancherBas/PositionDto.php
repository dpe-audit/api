<?php

namespace App\Dto\Enveloppe\PlancherBas;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\Position\Position;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/plancher_bas.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $surface_ue,
        public readonly ?float $perimetre_ue,
        public readonly ?string $local_non_chauffe_id,
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
