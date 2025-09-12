<?php

namespace App\Domain\Chauffage\Emetteur;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Generateur\{Generateur, GenerateurCollection};
use App\Domain\Chauffage\Installation\{Installation, InstallationCollection};
use App\Domain\Chauffage\Systeme\{Systeme, SystemeCollection};
use App\Domain\Common\ValueObject\Id;

final class Emetteur
{
    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private TypeEmetteur $type,
        private TypeEmission $type_emission,
        private TemperatureDistribution $temperature_distribution,
        private bool $presence_robinet_thermostatique,
        private ?int $annee_installation,
    ) {}

    public static function create(
        Id $id,
        Chauffage $chauffage,
        string $description,
        TypeEmetteur $type,
        TemperatureDistribution $temperature_distribution,
        bool $presence_robinet_thermostatique,
        ?int $annee_installation,
    ): self {
        return new self(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            type: $type,
            type_emission: TypeEmission::from_type_emetteur($type),
            temperature_distribution: $temperature_distribution,
            presence_robinet_thermostatique: $presence_robinet_thermostatique,
            annee_installation: $annee_installation,
        );
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

    public function type(): TypeEmetteur
    {
        return $this->type;
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return $this->temperature_distribution;
    }

    public function presence_robinet_thermostatique(): bool
    {
        return $this->presence_robinet_thermostatique;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function type_emission(): TypeEmission
    {
        return $this->type_emission;
    }

    /**
     * @return GenerateurCollection|Generateur[]
     */
    public function generateurs(): GenerateurCollection
    {
        return $this->chauffage->generateurs()->with_emetteur($this->id());
    }

    /**
     * @return GenerateurCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->chauffage->installations()->with_emetteur($this->id());
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->chauffage->systemes()->with_emetteur($this->id());
    }
}
