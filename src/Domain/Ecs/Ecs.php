<?php

namespace App\Domain\Ecs;

use App\Domain\Ecs\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Ecs\Installation\{Installation, InstallationCollection};
use App\Domain\Ecs\Systeme\{Systeme, SystemeCollection};
use Webmozart\Assert\Assert;

final class Ecs
{
    private GenerateurCollection $generateurs;
    private InstallationCollection $installations;
    private SystemeCollection $systemes;
    private EcsData $data;

    public function __construct()
    {
        $this->generateurs = new GenerateurCollection;
        $this->installations = new InstallationCollection;
        $this->systemes = new SystemeCollection;
        $this->data = EcsData::create();
    }

    public static function create(): self
    {
        return new self();
    }

    public function reinitialise(): self
    {
        $this->data = EcsData::create();
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
        $this->systemes->reinitialise();
        return $this;
    }

    public function calcule(EcsData $data): self
    {
        $this->data = $data;
        return $this;
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
        Assert::same($entity->ecs(), $this);

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
        Assert::same($entity->ecs(), $this);

        $this->installations->add($entity);
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

    public function add_systeme(Systeme $entity): self
    {
        Assert::null($this->systemes->find($entity->id()));
        Assert::same($entity->ecs(), $this);

        $this->systemes->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): EcsData
    {
        return $this->data;
    }
}
