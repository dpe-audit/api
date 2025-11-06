<?php

namespace App\Engine\Rules\Batiment;

use App\Domain\Common\Enum\Mois;

final class SollicitationsExterieures
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $epv,
        public readonly float $e,
        public readonly float $efr26,
        public readonly float $efr28,
        public readonly float $nref19,
        public readonly float $nref21,
        public readonly float $nref26,
        public readonly float $nref28,
        public readonly float $dh14,
        public readonly float $dh19,
        public readonly float $dh21,
        public readonly float $dh26,
        public readonly float $dh28,
        public readonly ?float $tefs,
        public readonly ?float $text,
        public readonly ?float $textmoy_clim26,
        public readonly ?float $textmoy_clim28,
    ) {}
}
