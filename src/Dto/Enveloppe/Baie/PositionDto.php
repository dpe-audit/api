<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Position\Mitoyennete;
use App\Domain\Enveloppe\Baie\Position\Position;
use App\Domain\Enveloppe\Baie\Position\TypePose;

final class PositionDto
{
    public function __construct(
        public float $surface,
        public Mitoyennete $mitoyennete,
        public ?TypePose $type_pose,
        public float $inclinaison,
        public ?float $orientation,
        public ?bool $presence_soubassement,
        public ?string $paroi_id,
        public ?string $local_non_chauffe_id,
        public ?string $double_fenetre_id,
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
