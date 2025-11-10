<?php

namespace App\Domain\Scenario\Etape;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Scenario\Scenario;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Ventilation\Ventilation;

final class Etape
{
    private EtapeData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Scenario $scenario,
        private string $nom,
        private string $description,
        private Enveloppe $enveloppe,
        private Chauffage $chauffage,
        private Ecs $ecs,
        private Refroidissement $refroidissement,
        private Ventilation $ventilation,
        private Production $production,
    ) {
        $this->data = EtapeData::create();
    }

    public static function create(
        Scenario $scenario,
        string $nom,
        string $description,
        Enveloppe $enveloppe,
        Chauffage $chauffage,
        Ecs $ecs,
        Refroidissement $refroidissement,
        Ventilation $ventilation,
        Production $production,
    ): self {
        return new static(
            id: Id::create(),
            scenario: $scenario,
            nom: $nom,
            description: $description,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            refroidissement: $refroidissement,
            ventilation: $ventilation,
            production: $production,
        );
    }

    public function calcule(EtapeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): self
    {
        $this->enveloppe->reinitialise();
        $this->chauffage->reinitialise();
        $this->ecs->reinitialise();
        $this->refroidissement->reinitialise();
        $this->ventilation->reinitialise();
        $this->production->reinitialise();
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function scenario(): Scenario
    {
        return $this->scenario;
    }

    public function nom(): string
    {
        return $this->nom;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function data(): EtapeData
    {
        return $this->data;
    }
}
