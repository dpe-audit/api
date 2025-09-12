<?php

namespace App\Domain\Ecs\Systeme;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Systeme\Reseau\Reseau;
use App\Domain\Ecs\Systeme\Stockage\Stockage;
use Webmozart\Assert\Assert;

final class Systeme
{
    private SystemeData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private Installation $installation,
        private Generateur $generateur,
        private string $description,
        private Reseau $reseau,
        private Stockage $stockage,
    ) {
        $this->data = SystemeData::create();
    }

    public static function create(
        Id $id,
        Ecs $ecs,
        Installation $installation,
        Generateur $generateur,
        string $description,
        Reseau $reseau,
        Stockage $stockage,
    ): self {
        Assert::same($ecs, $installation->ecs());
        Assert::same($ecs, $generateur?->ecs());

        return new self(
            id: $id,
            ecs: $ecs,
            installation: $installation,
            generateur: $generateur,
            description: $description,
            reseau: $reseau,
            stockage: $stockage,
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

    public function ecs(): Ecs
    {
        return $this->ecs;
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

    public function reseau(): Reseau
    {
        return $this->reseau;
    }

    public function stockage(): Stockage
    {
        return $this->stockage;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
