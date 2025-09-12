<?php

namespace App\Domain\Ventilation\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Installation\{Installation, InstallationCollection};
use App\Domain\Ventilation\Ventilation;

final class Generateur
{
    private GenerateurData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private TypeGenerateur $type,
        private bool $generateur_collectif,
        private ?bool $presence_echangeur_thermique,
        private ?int $annee_installation,
        private ?TypeVmc $type_vmc,
    ) {
        $this->data = GenerateurData::create();
    }

    public static function create(
        Id $id,
        Ventilation $ventilation,
        string $description,
        TypeGenerateur $type,
        bool $presence_echangeur_thermique,
        bool $generateur_collectif,
        ?int $annee_installation,
        ?TypeVmc $type_vmc,
    ): self {
        return new self(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            type: $type,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation,
            type_vmc: $type_vmc,
        );
    }

    public function calcule(GenerateurData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = GenerateurData::create();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function presence_echangeur_thermique(): ?bool
    {
        return $this->presence_echangeur_thermique;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function type_vmc(): ?TypeVmc
    {
        return $this->type_vmc;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->ventilation->installations()->with_generateur($this->id());
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
