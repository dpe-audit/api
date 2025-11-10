<?php

namespace App\Engine;

use App\Domain\Batiment\Batiment;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Diagnostic\Diagnostic;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Logement\Logement;
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Scenario\Etape\Etape;
use App\Domain\Ventilation\Ventilation;

final class Input
{
    public function __construct(
        public readonly Batiment $batiment,
        public readonly ?Logement $logement,
        public readonly Enveloppe $enveloppe,
        public readonly Chauffage $chauffage,
        public readonly Ecs $ecs,
        public readonly Refroidissement $refroidissement,
        public readonly Ventilation $ventilation,
        public readonly Production $production,
    ) {}

    public static function from_diagnostic(Diagnostic $entity, ?Logement $logement = null): self
    {
        return new self(
            batiment: $entity->batiment(),
            logement: $logement,
            enveloppe: $entity->enveloppe(),
            chauffage: $entity->chauffage(),
            ecs: $entity->ecs(),
            refroidissement: $entity->refroidissement(),
            ventilation: $entity->ventilation(),
            production: $entity->production(),
        );
    }

    public static function from_etape(Etape $entity, ?Logement $logement = null): self
    {
        return new self(
            batiment: $entity->scenario()->audit()->diagnostic()->batiment(),
            logement: $logement,
            enveloppe: $entity->enveloppe(),
            chauffage: $entity->chauffage(),
            ecs: $entity->ecs(),
            refroidissement: $entity->refroidissement(),
            ventilation: $entity->ventilation(),
            production: $entity->production(),
        );
    }
}
