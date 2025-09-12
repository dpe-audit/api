<?php

namespace App\Domain\Production\PanneauPhotovoltaique;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\Production;

final class PanneauPhotovoltaique
{
    private PanneauPhotovoltaiqueData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Production $production,
        private string $description,
        private float $orientation,
        private float $inclinaison,
        private int $modules,
        private ?float $surface,
        private bool $installation_collective,
    ) {
        $this->data = PanneauPhotovoltaiqueData::create();
    }

    public static function create(
        Id $id,
        Production $production,
        string $description,
        float $orientation,
        float $inclinaison,
        int $modules,
        ?float $surface,
        bool $installation_collective,
    ): self {
        return new self(
            id: $id,
            production: $production,
            description: $description,
            orientation: $orientation,
            inclinaison: $inclinaison,
            modules: $modules,
            surface: $surface,
            installation_collective: $installation_collective
        );
    }

    public function calcule(PanneauPhotovoltaiqueData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): self
    {
        $this->data = PanneauPhotovoltaiqueData::create();
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function inclinaison(): float
    {
        return $this->inclinaison;
    }

    public function orientation(): float
    {
        return $this->orientation;
    }

    public function modules(): int
    {
        return $this->modules;
    }

    public function surface(): ?float
    {
        return $this->surface;
    }

    public function installation_collective(): bool
    {
        return $this->installation_collective;
    }

    public function data(): PanneauPhotovoltaiqueData
    {
        return $this->data;
    }
}
