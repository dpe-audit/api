<?php

namespace App\Domain\Chauffage\Generateur;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Emetteur\{Emetteur, EmetteurCollection};
use App\Domain\Chauffage\Installation\{Installation, InstallationCollection};
use App\Domain\Chauffage\Systeme\{Systeme, SystemeCollection};
use App\Domain\Chauffage\Generateur\Position\Position;
use App\Domain\Chauffage\Generateur\Signaletique\Signaletique;
use App\Domain\Common\ValueObject\Id;

final class Generateur
{
    private GenerateurData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private ?TypeGenerateur $type,
        private ?EnergieGenerateur $energie,
        private ?EnergieGenerateur $bienergie,
        private ?int $annee_installation,
        private Position $position,
        private Signaletique $signaletique,
    ) {
        $this->data = GenerateurData::create();
    }

    public static function create(
        Id $id,
        Chauffage $chauffage,
        string $description,
        ?TypeGenerateur $type,
        ?EnergieGenerateur $energie,
        ?EnergieGenerateur $bienergie,
        ?int $annee_installation,
        Position $position,
        Signaletique $signaletique
    ): self {
        return new self(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            type: $type,
            energie: $energie,
            bienergie: $bienergie,
            annee_installation: $annee_installation,
            position: $position,
            signaletique: $signaletique
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

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
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

    public function bienergie(): ?EnergieGenerateur
    {
        return $this->bienergie;
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

    public function effet_joule(): bool
    {
        return $this->energie === EnergieGenerateur::ELECTRICITE;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->chauffage->installations()->with_generateur($this->id());
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->chauffage->systemes()->with_generateur($this->id());
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->chauffage->emetteurs()->with_generateur($this->id());
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
