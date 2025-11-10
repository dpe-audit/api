<?php

namespace App\Dto\Chauffage\Installation;

use App\Domain\Chauffage\Installation\{Installation, InstallationData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly bool $comptage_individuel,
        public readonly RegulationDto $regulation_centrale,
        public readonly RegulationDto $regulation_terminale,
        public readonly ?SolaireThermiqueDto $solaire_thermique,
        public readonly ?InstallationData $data = null,
    ) {}

    public static function from(Installation $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            comptage_individuel: $entity->comptage_individuel(),
            regulation_centrale: RegulationDto::from($entity->regulation_centrale()),
            regulation_terminale: RegulationDto::from($entity->regulation_terminale()),
            solaire_thermique: $entity->solaire_thermique() ? SolaireThermiqueDto::from($entity->solaire_thermique()) : null,
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'comptage_individuel' => $this->comptage_individuel,
            'regulation_centrale' => $this->regulation_centrale->__normalize(),
            'regulation_terminale' => $this->regulation_terminale->__normalize(),
            'solaire_thermique' => $this->solaire_thermique?->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'fch' => $this->data->fch,
                'rdim' => $this->data->rdim,
                'i0' => $this->data->i0,
                'int' => $this->data->int,
                'ich' => $this->data->ich,
                're' => $this->data->re,
                'rd' => $this->data->rd,
                'rg' => $this->data->rg,
                'rr' => $this->data->rr,
            ];
        }
        return $data;
    }
}
