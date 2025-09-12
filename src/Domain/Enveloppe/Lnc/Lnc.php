<?php

namespace App\Domain\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Lnc\Baie\{Baie, BaieCollection};
use App\Domain\Enveloppe\Lnc\Paroi\{Paroi, ParoiCollection};
use Webmozart\Assert\Assert;

final class Lnc
{
    private ParoiCollection $parois;
    private BaieCollection $baies;
    private LncData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeLnc $type,
    ) {
        $this->parois = new ParoiCollection();
        $this->baies = new BaieCollection();
        $this->data = LncData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeLnc $type,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
        );
    }

    public function reinitialise(): self
    {
        $this->data = LncData::create();
        $this->parois->reinitialise();
        $this->baies->reinitialise();
        return $this;
    }

    public function calcule(LncData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeLnc
    {
        return $this->type;
    }

    /**
     * @return BaieCollection|Baie[]
     */
    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        Assert::null($this->baies->find($entity->id()));
        $this->baies->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return ParoiCollection|Paroi[]
     */
    public function parois(): ParoiCollection
    {
        return $this->parois;
    }

    public function add_paroi(Paroi $entity): self
    {
        Assert::null($this->parois->find($entity->id()));
        $this->parois->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): LncData
    {
        return $this->data;
    }
}
