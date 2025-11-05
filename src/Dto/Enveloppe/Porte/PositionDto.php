<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\Porte\Position\{Position, TypePose};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/porte.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly TypePose $type_pose,
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $orientation,
        public readonly bool $presence_sas,
        public readonly ?string $paroi_id,
        public readonly ?string $local_non_chauffe_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            type_pose: $data->type_pose,
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
            orientation: $data->orientation,
            presence_sas: $data->presence_sas,
            paroi_id: $data->paroi?->id() ? (string) $data->paroi?->id() : null,
            local_non_chauffe_id: $data->local_non_chauffe?->id() ? (string) $data->local_non_chauffe?->id() : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'type_pose' => $this->type_pose->value,
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
            'orientation' => $this->orientation,
            'presence_sas' => $this->presence_sas,
            'paroi_id' => $this->paroi_id,
            'local_non_chauffe_id' => $this->local_non_chauffe_id,
        ];
    }
}
