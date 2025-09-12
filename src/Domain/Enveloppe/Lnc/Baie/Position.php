<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
        public readonly float $inclinaison,
        public readonly ?float $orientation,
    ) {}

    public static function create(
        Mitoyennete $mitoyennete,
        float $surface,
        float $inclinaison,
        ?float $orientation,
    ): self {
        return new self(
            mitoyennete: $mitoyennete,
            surface: $surface,
            inclinaison: $inclinaison,
            orientation: $orientation,
        );
    }
}
