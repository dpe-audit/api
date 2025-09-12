<?php

namespace App\Dto\Chauffage\Installation;

use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\Installation\InstallationCollection;

final class InstallationDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface,
        public bool $comptage_individuel,
        public RegulationDto $regulation_centrale,
        public RegulationDto $regulation_terminale,
        public ?SolaireThermiqueDto $solaire_thermique,
    ) {}

    public static function from(Installation $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface: $data->surface(),
            comptage_individuel: $data->comptage_individuel(),
            regulation_centrale: RegulationDto::from($data->regulation_centrale()),
            regulation_terminale: RegulationDto::from($data->regulation_terminale()),
            solaire_thermique: $data->solaire_thermique() ? SolaireThermiqueDto::from($data->solaire_thermique()) : null,
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(InstallationCollection $data): array
    {
        return $data->map(fn(Installation $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'comptage_individuel' => $this->comptage_individuel,
            'regulation_centrale' => $this->regulation_centrale->__normalize(),
            'regulation_terminale' => $this->regulation_terminale->__normalize(),
            'solaire_thermique' => $this->solaire_thermique?->__normalize(),
        ];
    }
}
