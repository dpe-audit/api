<?php

namespace App\Domain\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Ventilation\Installation\{Installation, InstallationCollection};
use Webmozart\Assert\Assert;

final class Ventilation
{
    private GenerateurCollection $generateurs;
    private InstallationCollection $installations;

    public function __construct()
    {
        $this->generateurs = new GenerateurCollection;
        $this->installations = new InstallationCollection;
    }

    public static function create(): self
    {
        return new self();
    }

    public function reinitialise(): void
    {
        $this->installations->reinitialise();
        $this->generateurs->reinitialise();
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
        Assert::same($entity->ventilation(), $this);

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
        Assert::same($entity->ventilation(), $this);

        $this->installations->add($entity);
        $this->reinitialise();

        return $this;
    }
}
