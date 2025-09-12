<?php

namespace App\Domain\Enveloppe\PontThermique;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\PontThermique\Liaison\Liaison;
use Webmozart\Assert\Assert;

final class PontThermique
{
    private PontThermiqueData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $longueur,
        private ?float $kpt,
        private Liaison $liaison,
    ) {
        $this->data = PontThermiqueData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        float $longueur,
        ?float $kpt,
        Liaison $liaison,
    ): self {
        Assert::same($liaison->mur->enveloppe(), $enveloppe);
        Assert::nullOrSame($liaison->plancher->enveloppe(), $enveloppe);
        Assert::nullOrSame($liaison->ouverture->enveloppe(), $enveloppe);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            longueur: $longueur,
            liaison: $liaison,
            kpt: $kpt,
        );
    }

    public function reinitialise(): self
    {
        $this->data = PontThermiqueData::create();
        return $this;
    }

    public function calcule(PontThermiqueData $data): self
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

    public function liaison(): Liaison
    {
        return $this->liaison;
    }

    public function longueur(): float
    {
        return $this->longueur;
    }

    public function kpt(): ?float
    {
        return $this->kpt;
    }

    public function data(): PontThermiqueData
    {
        return $this->data;
    }
}
