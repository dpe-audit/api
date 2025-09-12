<?php

namespace App\Domain\Enveloppe\PlancherBas\Position;

use App\Domain\Enveloppe\Lnc\Lnc;

final class Position
{
    public function __construct(
        public readonly float $surface,
        public readonly ?float $surface_ue,
        public readonly ?float $perimetre_ue,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Lnc $local_non_chauffe,
    ) {}

    public static function create(
        float $surface,
        ?float $surface_ue,
        ?float $perimetre_ue,
        Mitoyennete $mitoyennete,
        ?Lnc $local_non_chauffe,
    ): self {
        return new self(
            surface: $surface,
            surface_ue: $surface_ue,
            perimetre_ue: $perimetre_ue,
            mitoyennete: $mitoyennete,
            local_non_chauffe: $local_non_chauffe,
        );
    }
}
