<?php

namespace App\Domain\Enveloppe\Masque;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;

final class Masque
{
    private MasqueData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeMasque $type,
        private ConfigurationMasque $configuration,
        private ?float $hauteur,
        private ?float $profondeur,
        private ?Orientation $orientation,
        private ?SecteurMasque $secteur,
    ) {
        $this->data = MasqueData::create();
    }

    public function reinitialise(): self
    {
        $this->data = MasqueData::create();
        return $this;
    }

    public function calcule(MasqueData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeMasque $type,
        ConfigurationMasque $configuration,
        ?float $hauteur,
        ?float $profondeur,
        ?Orientation $orientation,
        ?SecteurMasque $secteur,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            configuration: $configuration,
            hauteur: $hauteur,
            profondeur: $profondeur,
            orientation: $orientation,
            secteur: $secteur,
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeMasque
    {
        return $this->type;
    }

    public function configuration(): ConfigurationMasque
    {
        return $this->configuration;
    }

    public function hauteur(): ?float
    {
        return $this->hauteur;
    }

    public function profondeur(): ?float
    {
        return $this->profondeur;
    }

    public function orientation(): ?Orientation
    {
        return $this->orientation;
    }

    public function secteur(): ?SecteurMasque
    {
        return $this->secteur;
    }

    public function data(): MasqueData
    {
        return $this->data;
    }
}
