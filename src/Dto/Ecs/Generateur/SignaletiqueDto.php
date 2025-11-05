<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Signaletique\{LabelGenerateur, ModeCombustion, Signaletique};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/generateur.yaml
 */
final class SignaletiqueDto
{
    public function __construct(
        public readonly ?int $volume_stockage,
        public readonly ?float $pn,
        public readonly ?LabelGenerateur $label,
        public readonly ?float $cop,
        public readonly ?ModeCombustion $mode_combustion,
        public readonly ?bool $presence_ventouse,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?float $rpn,
    ) {}

    public static function from(Signaletique $data): self
    {
        return new self(
            volume_stockage: $data->volume_stockage,
            pn: $data->pn,
            label: $data->label,
            cop: $data->cop,
            mode_combustion: $data->mode_combustion,
            presence_ventouse: $data->presence_ventouse,
            pveilleuse: $data->pveilleuse,
            qp0: $data->qp0,
            rpn: $data->rpn,
        );
    }

    public function __normalize(): array
    {
        return [
            'volume_stockage' => $this->volume_stockage,
            'pn' => $this->pn,
            'label' => $this->label?->value,
            'cop' => $this->cop,
            'mode_combustion' => $this->mode_combustion?->value,
            'presence_ventouse' => $this->presence_ventouse,
            'pveilleuse' => $this->pveilleuse,
            'qp0' => $this->qp0,
            'rpn' => $this->rpn,
        ];
    }
}
