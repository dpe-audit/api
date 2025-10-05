<?php

namespace App\Domain\Enveloppe\PlancherHaut\Position;

use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Paroi\Mitoyennete;

final class Position
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?float $orientation,
        public readonly ?Lnc $local_non_chauffe,
    ) {}

    public static function create(
        float $surface,
        Mitoyennete $mitoyennete,
        ?float $orientation,
        ?Lnc $local_non_chauffe,
    ): self {
        return new self(
            surface: $surface,
            mitoyennete: $mitoyennete,
            orientation: $orientation,
            local_non_chauffe: $local_non_chauffe,
        );
    }
}
