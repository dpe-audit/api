<?php

namespace App\Dto\Ecs\Installation;

use App\Domain\Ecs\Installation\{Installation, InstallationData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly ?SolaireThermiqueDto $solaire_thermique,
        public readonly ?InstallationData $data = null,
    ) {}

    public static function from(Installation $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
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
            'solaire_thermique' => $this->solaire_thermique?->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'fecs' => $this->data->fecs,
                'rdim' => $this->data->rdim,
                'iecs' => $this->data->iecs,
                'rd' => $this->data->rd,
                'rs' => $this->data->rs,
                'rg' => $this->data->rg,
                'rgs' => $this->data->rgs,
                'consommations' => $this->data->consommations?->__normalize(),
            ];
        }
        return $data;
    }
}
