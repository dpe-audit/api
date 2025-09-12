<?php

namespace App\Domain\Ecs\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Generateur\Position\Position;
use App\Domain\Ecs\Generateur\Signaletique\Signaletique;
use App\Domain\Ecs\Installation\{Installation, InstallationCollection};
use App\Domain\Ecs\Systeme\{Systeme, SystemeCollection};

final class Generateur
{
    private GenerateurData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private ?TypeGenerateur $type,
        private ?EnergieGenerateur $energie,
        private ?int $annee_installation,
        private Position $position,
        private Signaletique $signaletique,
    ) {
        $this->data = GenerateurData::create();
    }

    public static function create(
        Id $id,
        Ecs $ecs,
        string $description,
        ?TypeGenerateur $type,
        ?EnergieGenerateur $energie,
        ?int $annee_installation,
        Position $position,
        Signaletique $signaletique,
    ): self {
        return new self(
            id: $id,
            ecs: $ecs,
            description: $description,
            type: $type,
            energie: $energie,
            annee_installation: $annee_installation,
            position: $position,
            signaletique: $signaletique,
        );
    }

    public function reinitialise(): self
    {
        $this->data = GenerateurData::create();
        return $this;
    }

    public function calcule(GenerateurData $data): self
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

    public function type(): ?TypeGenerateur
    {
        return $this->type;
    }

    public function energie(): ?EnergieGenerateur
    {
        return $this->energie;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->ecs->installations()->with_generateur($this->id());
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->ecs->systemes()->with_generateur($this->id());
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
