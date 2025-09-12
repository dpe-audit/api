<?php

namespace App\Domain\Refroidissement\Systeme;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Generateur\Generateur;
use App\Domain\Refroidissement\Installation\Installation;
use App\Domain\Refroidissement\Refroidissement;
use Webmozart\Assert\Assert;

final class Systeme
{
    private SystemeData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private string $description,
        private Installation $installation,
        private Generateur $generateur,
    ) {
        $this->data = SystemeData::create();
    }

    public static function create(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        Installation $installation,
        Generateur $generateur,
    ): self {
        Assert::same($refroidissement, $installation->refroidissement());
        Assert::same($refroidissement, $generateur?->refroidissement());

        return new self(
            id: $id,
            refroidissement: $refroidissement,
            description: $description,
            installation: $installation,
            generateur: $generateur,
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

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
