<?php

namespace App\Domain\Scenario;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Scenario\Etape\{Etape, EtapeCollection};
use Webmozart\Assert\Assert;

final class Scenario
{
    private EtapeCollection $etapes;

    public function __construct(
        private readonly Id $id,
        private readonly Audit $audit,
        private TypeScenario $type,
        private string $nom,
        private string $description,
    ) {
        $this->etapes = new EtapeCollection();
    }

    public static function create(
        Audit $audit,
        TypeScenario $type,
        string $nom,
        string $description,
    ): self {
        return new static(
            id: Id::create(),
            audit: $audit,
            type: $type,
            nom: $nom,
            description: $description,
        );
    }

    public function reinitialise(): self
    {
        $this->etapes->reinitialise();
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function type(): TypeScenario
    {
        return $this->type;
    }

    public function nom(): string
    {
        return $this->nom;
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return EtapeCollection|Etape[]
     */
    public function etapes(): EtapeCollection
    {
        return $this->etapes;
    }

    public function add_etape(Etape $entity): self
    {
        Assert::null($this->etapes->find($entity->id()));
        Assert::same($entity->scenario(), $this);

        $this->etapes->add($entity);
        $this->reinitialise();

        return $this;
    }
}
