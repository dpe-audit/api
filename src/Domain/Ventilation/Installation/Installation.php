<?php

namespace App\Domain\Ventilation\Installation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Generateur\Generateur;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Installation
{
    private InstallationData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private float $surface,
        private TypeVentilation $type,
        private ?Generateur $generateur,
    ) {
        $this->data = InstallationData::create();
    }

    public static function create(
        Id $id,
        Ventilation $ventilation,
        string $description,
        float $surface,
        TypeVentilation $type,
        ?Generateur $generateur,
    ): self {
        Assert::nullOrSame($generateur?->ventilation(), $ventilation);

        return new self(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            surface: $surface,
            type: $type,
            generateur: $generateur,
        );
    }

    public function calcule(InstallationData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = InstallationData::create();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function type(): TypeVentilation
    {
        return $this->type;
    }

    public function generateur(): ?Generateur
    {
        return $this->generateur;
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
