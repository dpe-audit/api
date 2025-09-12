<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\ModeCombustion;
use App\Domain\Ecs\Generateur\Signaletique\Signaletique;

final class SignaletiqueDto
{
    public function __construct(
        public ?int $volume_stockage,
        public ?float $pn,
        public ?LabelGenerateur $label,
        public ?float $cop,
        public ?ModeCombustion $mode_combustion,
        public ?bool $presence_ventouse,
        public ?float $pveilleuse,
        public ?float $qp0,
        public ?float $rpn,
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
