<?php

namespace App\Domain\Enveloppe\Lnc\Paroi;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
    ) {}

    public static function create(Mitoyennete $mitoyennete, float $surface,): self
    {
        return new self(mitoyennete: $mitoyennete, surface: $surface,);
    }
}
