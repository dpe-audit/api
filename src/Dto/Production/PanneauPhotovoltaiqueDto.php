<?php

namespace App\Dto\Production;

use App\Domain\Production\PanneauPhotovoltaique\{PanneauPhotovoltaique, PanneauPhotovoltaiqueData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/production/panneau_photovoltaique.yaml
 */
final class PanneauPhotovoltaiqueDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $orientation,
        public readonly float $inclinaison,
        public readonly int $modules,
        public readonly ?float $surface,
        public readonly bool $installation_collective,
        public readonly ?PanneauPhotovoltaiqueData $data = null,
    ) {}

    public static function from(PanneauPhotovoltaique $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            orientation: $entity->orientation(),
            inclinaison: $entity->inclinaison(),
            modules: $entity->modules(),
            surface: $entity->surface(),
            installation_collective: $entity->installation_collective(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'orientation' => $this->orientation,
            'inclinaison' => $this->inclinaison,
            'modules' => $this->modules,
            'surface' => $this->surface,
            'installation_collective' => $this->installation_collective,
        ];
        if ($this->data) {
            $data['data'] = [
                'kpv' => $this->data->kpv,
                'ppv' => $this->data->ppv,
            ];
        }
        return $data;
    }
}
