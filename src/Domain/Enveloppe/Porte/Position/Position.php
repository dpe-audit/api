<?php

namespace App\Domain\Enveloppe\Porte\Position;

use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Paroi\Paroi;

final class Position
{
    public function __construct(
        public readonly bool $presence_sas,
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $orientation,
        public readonly ?Paroi $paroi,
        public readonly ?Lnc $local_non_chauffe,
    ) {}

    public static function create(
        bool $presence_sas,
        float $surface,
        Mitoyennete $mitoyennete,
        ?float $orientation,
        ?Paroi $paroi,
        ?Lnc $local_non_chauffe,
    ): self {
        return new self(
            presence_sas: $presence_sas,
            surface: $surface,
            mitoyennete: $mitoyennete,
            orientation: $orientation,
            paroi: $paroi,
            local_non_chauffe: $local_non_chauffe,
        );
    }
}
