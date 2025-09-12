<?php

namespace App\Dto\Chauffage\Emetteur;

use App\Domain\Chauffage\Emetteur\Emetteur;
use App\Domain\Chauffage\Emetteur\EmetteurCollection;
use App\Domain\Chauffage\Emetteur\TemperatureDistribution;
use App\Domain\Chauffage\Emetteur\TypeEmetteur;

final class EmetteurDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeEmetteur $type,
        public TemperatureDistribution $temperature_distribution,
        public bool $presence_robinet_thermostatique,
        public ?int $annee_installation,
    ) {}

    public static function from(Emetteur $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            temperature_distribution: $data->temperature_distribution(),
            presence_robinet_thermostatique: $data->presence_robinet_thermostatique(),
            annee_installation: $data->annee_installation(),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(EmetteurCollection $data): array
    {
        return $data->map(fn(Emetteur $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'temperature_distribution' => $this->temperature_distribution->value,
            'presence_robinet_thermostatique' => $this->presence_robinet_thermostatique,
            'annee_installation' => $this->annee_installation,
        ];
    }
}
