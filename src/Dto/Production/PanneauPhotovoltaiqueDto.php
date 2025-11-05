<?php

namespace App\Dto\Production;

use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;

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
    ) {}

    public static function from(PanneauPhotovoltaique $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            orientation: $data->orientation(),
            inclinaison: $data->inclinaison(),
            modules: $data->modules(),
            surface: $data->surface(),
            installation_collective: $data->installation_collective(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'orientation' => $this->orientation,
            'inclinaison' => $this->inclinaison,
            'modules' => $this->modules,
            'surface' => $this->surface,
            'installation_collective' => $this->installation_collective,
        ];
    }
}
