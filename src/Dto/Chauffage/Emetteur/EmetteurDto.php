<?php

namespace App\Dto\Chauffage\Emetteur;

use App\Domain\Chauffage\Emetteur\{Emetteur, TemperatureDistribution, TypeEmetteur};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/emetteur.yaml
 */
final class EmetteurDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeEmetteur $type,
        public readonly TemperatureDistribution $temperature_distribution,
        public readonly bool $presence_robinet_thermostatique,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
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
