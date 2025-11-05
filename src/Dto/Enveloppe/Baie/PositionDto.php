<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Position\{Position, TypePose};
use App\Domain\Enveloppe\Paroi\Mitoyennete;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/baie.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?TypePose $type_pose,
        public readonly float $inclinaison,
        public readonly ?float $orientation,
        public readonly ?bool $presence_soubassement,
        public readonly ?string $paroi_id,
        public readonly ?string $local_non_chauffe_id,
        public readonly ?string $double_fenetre_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            surface: $data->surface,
            mitoyennete: $data->mitoyennete,
            type_pose: $data->type_pose,
            inclinaison: $data->inclinaison,
            orientation: $data->orientation,
            presence_soubassement: $data->presence_soubassement,
            paroi_id: $data->paroi ? (string) $data->paroi->id() : null,
            local_non_chauffe_id: $data->local_non_chauffe ? (string) $data->local_non_chauffe->id() : null,
            double_fenetre_id: $data->double_fenetre ? (string) $data->double_fenetre->id() : null,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'mitoyennete' => $this->mitoyennete->value,
            'type_pose' => $this->type_pose?->value,
            'inclinaison' => $this->inclinaison,
            'orientation' => $this->orientation,
            'presence_soubassement' => $this->presence_soubassement,
            'paroi_id' => $this->paroi_id,
            'local_non_chauffe_id' => $this->local_non_chauffe_id,
            'double_fenetre_id' => $this->double_fenetre_id,
        ];
    }
}
