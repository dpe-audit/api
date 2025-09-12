<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Apports\Apports;
use App\Domain\Enveloppe\ConfortEte\ConfortEte;
use App\Domain\Enveloppe\Deperditions\Deperditions;
use App\Domain\Enveloppe\Permeabilite\Permeabilite;

final class EnveloppeData
{
    public function __construct(
        public readonly ?Inertie $inertie,
        public readonly Permeabilite $permeabilite,
        public readonly Deperditions $deperditions,
        public readonly ConfortEte $confort_ete,
        public readonly Apports $apports,
    ) {}

    public static function create(
        ?Inertie $inertie = null,
        ?Permeabilite $permeabilite = null,
        ?Deperditions $deperditions = null,
        ?ConfortEte $confort_ete = null,
        ?Apports $apports = null,
    ): self {
        return new self(
            inertie: $inertie,
            permeabilite: $permeabilite ?? Permeabilite::create(),
            deperditions: $deperditions ?? Deperditions::create(),
            confort_ete: $confort_ete ?? ConfortEte::create(),
            apports: $apports ?? Apports::create(),
        );
    }

    public function with(
        ?Inertie $inertie = null,
        ?Permeabilite $permeabilite = null,
        ?Deperditions $deperditions = null,
        ?ConfortEte $confort_ete = null,
        ?Apports $apports = null,
    ): self {
        return self::create(
            inertie: $inertie ?? $this->inertie,
            permeabilite: $permeabilite ?? $this->permeabilite,
            deperditions: $deperditions ?? $this->deperditions,
            confort_ete: $confort_ete ?? $this->confort_ete,
            apports: $apports ?? $this->apports,
        );
    }
}
