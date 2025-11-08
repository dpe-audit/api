<?php

namespace App\Domain\Chauffage\Generateur\Signaletique;

final class Signaletique
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

    public static function create(
        ?float $pn,
        ?LabelGenerateur $label,
        ?float $scop,
        ?ModeCombustion $mode_combustion,
        ?bool $presence_ventouse,
        ?bool $presence_regulation_combustion,
        ?float $pveilleuse,
        ?float $qp0,
        ?float $rpn,
        ?float $rpint,
        ?float $tfonc30,
        ?float $tfonc100,
    ): self {
        return new self(
            pn: $pn,
            label: $label,
            scop: $scop,
            mode_combustion: $mode_combustion,
            presence_ventouse: $presence_ventouse,
            presence_regulation_combustion: $presence_regulation_combustion,
            pveilleuse: $pveilleuse,
            qp0: $qp0,
            rpn: $rpn,
            rpint: $rpint,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
        );
    }
}
