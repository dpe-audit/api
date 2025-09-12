<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\Position\Position;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;

final class PositionDto
{
    public function __construct(
        public float $inclinaison,
        public ?TypePose $type_pose,
        public ?bool $presence_soubassement,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            type_pose: $data->type_pose,
            inclinaison: $data->inclinaison,
            presence_soubassement: $data->presence_soubassement,
        );
    }

    public function __normalize(): array
    {
        return [
            'inclinaison' => $this->inclinaison,
            'type_pose' => $this->type_pose?->value,
            'presence_soubassement' => $this->presence_soubassement,
        ];
    }
}
