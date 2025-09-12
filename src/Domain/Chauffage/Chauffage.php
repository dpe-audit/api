<?php

namespace App\Domain\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, EmetteurCollection};
use App\Domain\Chauffage\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Chauffage\Installation\{Installation, InstallationCollection};
use App\Domain\Chauffage\Systeme\{Systeme, SystemeCollection};
use Webmozart\Assert\Assert;

final class Chauffage
{
    private GenerateurCollection $generateurs;
    private EmetteurCollection $emetteurs;
    private InstallationCollection $installations;
    private SystemeCollection $systemes;
    private ChauffageData $data;

    public function __construct()
    {
        $this->generateurs = new GenerateurCollection;
        $this->emetteurs = new EmetteurCollection;
        $this->installations = new InstallationCollection;
        $this->systemes = new SystemeCollection;
        $this->data = ChauffageData::create();
    }

    public static function create(): self
    {
        return new self();
    }

    public function reinitialise(): self
    {
        $this->data = ChauffageData::create();
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
        $this->systemes->reinitialise();
        return $this;
    }

    public function calcule(ChauffageData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function effet_joule(): bool
    {
        return $this->installations->effet_joule();
    }

    /**
     * @return GenerateurCollection|Generateur[]
     */
    public function generateurs(): GenerateurCollection
    {
        return $this->generateurs;
    }

    public function add_generateur(Generateur $entity): self
    {
        Assert::null($this->generateurs->find($entity->id()));
        Assert::same($entity->chauffage(), $this);

        $this->generateurs->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->installations;
    }

    public function add_installation(Installation $entity): self
    {
        Assert::null($this->installations->find($entity->id()));
        Assert::same($entity->chauffage(), $this);

        $this->installations->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function add_emetteur(Emetteur $entity): self
    {
        Assert::null($this->emetteurs->find($entity->id()));
        Assert::same($entity->chauffage(), $this);

        $this->emetteurs->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->systemes;
    }

    /**
     * On limite le nombre de systèmes centraux à 2 par installation
     */
    public function add_systeme(Systeme $entity): self
    {
        Assert::null($this->systemes->find($entity->id()));
        Assert::same($entity->chauffage(), $this);

        $this->systemes->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): ChauffageData
    {
        return $this->data;
    }
}
