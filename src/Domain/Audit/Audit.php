<?php

namespace App\Domain\Audit;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Diagnostic\Diagnostic;
use App\Domain\Scenario\{Scenario, ScenarioCollection};
use Webmozart\Assert\Assert;

final class Audit
{
    private ScenarioCollection $scenarios;

    public function __construct(
        private readonly Id $id,
        private readonly Diagnostic $diagnostic,
        private readonly \DateTimeImmutable $date,
        private readonly \DateTimeImmutable $date_visite,
        private readonly \DateTimeImmutable $date_etablissement,
    ) {
        $this->scenarios = new ScenarioCollection();
    }

    public static function create(
        Diagnostic $diagnostic,
        \DateTimeImmutable $date_visite,
        \DateTimeImmutable $date_etablissement,
    ): self {
        return new self(
            id: Id::create(),
            diagnostic: $diagnostic,
            date: new \DateTimeImmutable(),
            date_visite: $date_visite,
            date_etablissement: $date_etablissement,
        );
    }

    public function reinitialise(): self
    {
        $this->scenarios->reinitialise();
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function diagnostic(): Diagnostic
    {
        return $this->diagnostic;
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

    /**
     * @return ScenarioCollection|Scenario[]
     */
    public function scenarios(): ScenarioCollection
    {
        return $this->scenarios;
    }

    public function add_scenario(Scenario $entity): self
    {
        Assert::null($this->scenarios->find($entity->id()));
        Assert::same($entity->audit(), $this);

        $this->scenarios->add($entity);
        $this->reinitialise();

        return $this;
    }
}
