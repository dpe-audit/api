<?php

namespace App\Domain\Enveloppe\DoubleFenetre\Position;

final class Position
{
    public function __construct(
        public readonly float $inclinaison,
        public readonly ?TypePose $type_pose,
        public readonly ?bool $presence_soubassement,
    ) {}

    public static function create(
        float $inclinaison,
        ?TypePose $type_pose,
        ?bool $presence_soubassement = null
    ): self {
        return new self(
            type_pose: $type_pose,
            inclinaison: $inclinaison,
            presence_soubassement: $presence_soubassement,
        );
    }
}
