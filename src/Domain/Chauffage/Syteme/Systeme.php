<?php

namespace App\Domain\Chauffage\Systeme;

use App\Domain\Chauffage\{Chauffage, TypeChauffage};
use App\Domain\Chauffage\Emetteur\{Emetteur, EmetteurCollection};
use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\Systeme\Reseau\Reseau;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Systeme
{
    private EmetteurCollection $emetteurs;
    private SystemeData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private readonly Installation $installation,
        private readonly Generateur $generateur,
        private string $description,
        private TypeChauffage $type,
        private ?Reseau $reseau,
    ) {
        $this->emetteurs = new EmetteurCollection();
        $this->data = SystemeData::create();
    }

    public static function create(
        Id $id,
        Chauffage $chauffage,
        Installation $installation,
        Generateur $generateur,
        string $description,
        TypeChauffage $type,
        ?Reseau $reseau,
    ): self {
        Assert::notNull($chauffage->installations()->find($installation->id()));
        Assert::notNull($chauffage->generateurs()->find($generateur->id()));

        return new self(
            id: $id,
            chauffage: $chauffage,
            installation: $installation,
            generateur: $generateur,
            description: $description,
            type: $type,
            reseau: $reseau,
        );
    }

    public function reinitialise(): self
    {
        $this->data = SystemeData::create();
        return $this;
    }

    public function calcule(SystemeData $data): self
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

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function effet_joule(): bool
    {
        return $this->generateur->effet_joule();
    }

    public function type(): TypeChauffage
    {
        return $this->type;
    }

    public function reseau(): ?Reseau
    {
        return $this->reseau;
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function reference_emetteur(Emetteur $entity): self
    {
        Assert::notNull($this->chauffage->emetteurs()->find($entity->id()));
        Assert::null($this->emetteurs->find($entity->id()));

        $this->emetteurs->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
