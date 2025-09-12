<?php

namespace App\Domain\Refroidissement\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Reseau\Reseau;

final class Generateur
{
    private GenerateurData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private ?Reseau $reseau_froid,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private ?int $annee_installation,
        private ?float $seer,
    ) {
        $this->data = GenerateurData::create();
    }

    public static function create(
        Id $id,
        Refroidissement $refroidissement,
        ?Reseau $reseau_froid,
        string $description,
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        ?int $annee_installation,
        ?float $seer,
    ): self {
        return new self(
            id: $id,
            refroidissement: $refroidissement,
            reseau_froid: $reseau_froid,
            description: $description,
            type: $type,
            energie: $energie,
            annee_installation: $annee_installation,
            seer: $seer,
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

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function reseau_froid(): ?Reseau
    {
        return $this->reseau_froid;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function seer(): ?float
    {
        return $this->seer;
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
