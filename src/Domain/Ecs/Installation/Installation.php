<?php

namespace App\Domain\Ecs\Installation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Ecs\Installation\Solaire\Solaire;
use App\Domain\Ecs\Systeme\{Systeme, SystemeCollection};

final class Installation
{
    private InstallationData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private float $surface,
        private ?Solaire $solaire_thermique,
    ) {
        $this->data = InstallationData::create();
    }

    public static function create(
        Id $id,
        Ecs $ecs,
        string $description,
        float $surface,
        ?Solaire $solaire_thermique,
    ): self {
        return new self(
            id: $id,
            ecs: $ecs,
            description: $description,
            surface: $surface,
            solaire_thermique: $solaire_thermique,
        );
    }

    public function reinitialise(): self
    {
        $this->data = InstallationData::create();
        return $this;
    }

    public function calcule(InstallationData $data): self
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

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function solaire_thermique(): ?Solaire
    {
        return $this->solaire_thermique;
    }

    /**
     * @return GenerateurCollection|Generateur[]
     */
    public function generateurs(): GenerateurCollection
    {
        return $this->ecs->generateurs()->with_installation($this->id());
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->ecs->systemes()->with_installation($this->id());
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
