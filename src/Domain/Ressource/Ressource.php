<?php

namespace App\Domain\Ressource;

use App\Domain\Adresse\Adresse;
use App\Domain\Batiment\Batiment;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Eclairage\Eclairage;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Logement\{Logement, LogementCollection};
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Ressource
{
    private LogementCollection $logements;
    private RessourceData $data;

    public function __construct(
        private readonly Id $id,
        private readonly \DateTimeImmutable $date,
        private readonly \DateTimeImmutable $date_visite,
        private readonly \DateTimeImmutable $date_etablissement,
        private Adresse $adresse,
        private Batiment $batiment,
        private Enveloppe $enveloppe,
        private Chauffage $chauffage,
        private Ecs $ecs,
        private Refroidissement $refroidissement,
        private Ventilation $ventilation,
        private Production $production,
        private Eclairage $eclairage,
    ) {
        $this->logements = new LogementCollection();
        $this->data = RessourceData::create();
    }

    public static function create(
        \DateTimeImmutable $date_visite,
        \DateTimeImmutable $date_etablissement,
        Adresse $adresse,
        Batiment $batiment,
        Enveloppe $enveloppe,
        Chauffage $chauffage,
        Ecs $ecs,
        Refroidissement $refroidissement,
        Ventilation $ventilation,
        Production $production,
    ): self {
        return new static(
            id: Id::create(),
            date: new \DateTimeImmutable(),
            date_visite: $date_visite,
            date_etablissement: $date_etablissement,
            adresse: $adresse,
            batiment: $batiment,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            refroidissement: $refroidissement,
            ventilation: $ventilation,
            production: $production,
            eclairage: Eclairage::create(),
        );
    }

    public function calcule(RessourceData $data): self
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
        $this->eclairage->reinitialise();
        return $this;
    }

    public function data(): RessourceData
    {
        return $this->data;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function date_visite(): \DateTimeImmutable
    {
        return $this->date_visite;
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        return $this->date_etablissement;
    }

    public function adresse(): Adresse
    {
        return $this->adresse;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
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

    public function eclairage(): Eclairage
    {
        return $this->eclairage;
    }

    public function logements(): LogementCollection
    {
        return $this->logements;
    }

    public function add_logement(Logement $entity): Ressource
    {
        Assert::null($this->logements->find($entity->id()));
        Assert::same($entity->ressource(), $this);

        $this->logements->add($entity);
        $this->reinitialise();

        return $this;
    }
}
