<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\Position\{Position, TypePose};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/double_fenetre.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly float $inclinaison,
        public readonly ?TypePose $type_pose,
        public readonly ?bool $presence_soubassement,
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
