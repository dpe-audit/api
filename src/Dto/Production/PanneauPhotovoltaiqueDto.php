<?php

namespace App\Dto\Production;

use App\Domain\Production\PanneauPhotovoltaique\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};

final class PanneauPhotovoltaiqueDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $orientation,
        public float $inclinaison,
        public int $modules,
        public ?float $surface,
        public bool $installation_collective,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(PanneauPhotovoltaiqueCollection $data): array
    {
        return $data->map(fn(PanneauPhotovoltaique $item): self => self::from($item))->values();
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
