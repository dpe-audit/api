<?php

namespace App\Domain\Ecs\Generateur\Signaletique;

final class Signaletique
{
    public function __construct(
        public readonly ?float $volume_stockage,
        public readonly ?float $pn,
        public readonly ?LabelGenerateur $label,
        public readonly ?float $cop,
        public readonly ?ModeCombustion $mode_combustion,
        public readonly ?bool $presence_ventouse,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?Float $rpn,
    ) {}

    public static function create(
        ?float $volume_stockage,
        ?float $pn,
        ?LabelGenerateur $label,
        ?float $cop,
        ?ModeCombustion $mode_combustion,
        ?bool $presence_ventouse,
        ?float $pveilleuse,
        ?float $qp0,
        ?Float $rpn,
    ): self {
        return new self(
            volume_stockage: $volume_stockage,
            pn: $pn,
            label: $label,
            cop: $cop,
            mode_combustion: $mode_combustion,
            presence_ventouse: $presence_ventouse,
            pveilleuse: $pveilleuse,
            qp0: $qp0,
            rpn: $rpn,
        );
    }
}
