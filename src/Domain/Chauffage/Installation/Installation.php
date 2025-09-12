<?php

namespace App\Domain\Chauffage\Installation;

use App\Domain\Chauffage\{Chauffage, TypeChauffage};
use App\Domain\Chauffage\Emetteur\{Emetteur, EmetteurCollection};
use App\Domain\Chauffage\Generateur\GenerateurCollection;
use App\Domain\Chauffage\Installation\Regulation\Regulation;
use App\Domain\Chauffage\Installation\Solaire\Solaire;
use App\Domain\Chauffage\Systeme\{Systeme, SystemeCollection};
use App\Domain\Common\ValueObject\Id;

final class Installation
{
    private InstallationData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private float $surface,
        private bool $comptage_individuel,
        private ?Solaire $solaire_thermique,
        private Regulation $regulation_centrale,
        private Regulation $regulation_terminale,
    ) {
        $this->data = InstallationData::create();
    }

    public static function create(
        Id $id,
        Chauffage $chauffage,
        string $description,
        float $surface,
        bool $comptage_individuel,
        ?Solaire $solaire_thermique,
        Regulation $regulation_centrale,
        Regulation $regulation_terminale,
    ): self {
        return new self(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            surface: $surface,
            solaire_thermique: $solaire_thermique,
            regulation_centrale: $regulation_centrale,
            regulation_terminale: $regulation_terminale,
            comptage_individuel: $comptage_individuel,
        );
    }

    public function reinitialise(): self
    {
        $this->data = InstallationData::create();
        return $this;
    }

    public function calcule(InstallationData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function solaire_thermique(): ?Solaire
    {
        return $this->solaire_thermique;
    }

    public function regulation_centrale(): Regulation
    {
        return $this->regulation_centrale;
    }

    public function regulation_terminale(): Regulation
    {
        return $this->regulation_terminale;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function comptage_individuel(): bool
    {
        return $this->comptage_individuel;
    }

    public function installation_collective(): bool
    {
        return $this->systemes()->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)->has_generateur_collectif();
    }

    public function effet_joule(): bool
    {
        return $this->systemes()->effet_joule();
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->chauffage->systemes()->with_installation($this->id);
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->chauffage->emetteurs()->with_installation($this->id());
    }

    /**
     * @return GenerateurCollection|Generateur[]
     */
    public function generateurs(): GenerateurCollection
    {
        return $this->chauffage->generateurs()->with_installation($this->id());
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
