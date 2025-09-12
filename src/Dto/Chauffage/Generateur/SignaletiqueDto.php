<?php

namespace App\Dto\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Domain\Chauffage\Generateur\Signaletique\Signaletique;

final class SignaletiqueDto
{
    public function __construct(
        public ?float $pn,
        public ?LabelGenerateur $label,
        public ?float $scop,
        public ?ModeCombustion $mode_combustion,
        public ?bool $presence_ventouse,
        public ?bool $presence_regulation_combustion,
        public ?float $pveilleuse,
        public ?float $qp0,
        public ?float $rpn,
        public ?float $rpint,
        public ?float $tfonc30,
        public ?float $tfonc100,
    ) {}

    public static function from(Signaletique $data): self
    {
        return new self(
            pn: $data->pn,
            label: $data->label,
            scop: $data->scop,
            mode_combustion: $data->mode_combustion,
            presence_ventouse: $data->presence_ventouse,
            presence_regulation_combustion: $data->presence_regulation_combustion,
            pveilleuse: $data->pveilleuse,
            qp0: $data->qp0,
            rpn: $data->rpn,
            rpint: $data->rpint,
            tfonc30: $data->tfonc30,
            tfonc100: $data->tfonc100,
        );
    }

    public function __normalize(): array
    {
        return [
            'pn' => $this->pn,
            'label' => $this->label?->value,
            'scop' => $this->scop,
            'mode_combustion' => $this->mode_combustion?->value,
            'presence_ventouse' => $this->presence_ventouse,
            'presence_regulation_combustion' => $this->presence_regulation_combustion,
            'pveilleuse' => $this->pveilleuse,
            'qp0' => $this->qp0,
            'rpn' => $this->rpn,
            'rpint' => $this->rpint,
            'tfonc30' => $this->tfonc30,
            'tfonc100' => $this->tfonc100,
        ];
    }
}
