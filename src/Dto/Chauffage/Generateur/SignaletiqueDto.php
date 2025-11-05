<?php

namespace App\Dto\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Domain\Chauffage\Generateur\Signaletique\Signaletique;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/generateur.yaml
 */
final class SignaletiqueDto
{
    public function __construct(
        public readonly ?float $pn,
        public readonly ?LabelGenerateur $label,
        public readonly ?float $scop,
        public readonly ?ModeCombustion $mode_combustion,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $presence_regulation_combustion,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
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
