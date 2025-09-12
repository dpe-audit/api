<?php

namespace App\Dto\Enveloppe\Masque;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;
use App\Domain\Enveloppe\Masque\Masque;
use App\Domain\Enveloppe\Masque\MasqueCollection;
use App\Domain\Enveloppe\Masque\SecteurMasque;
use App\Domain\Enveloppe\Masque\TypeMasque;

final class MasqueDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeMasque $type,
        public ConfigurationMasque $configuration,
        public ?Orientation $orientation,
        public ?float $hauteur,
        public ?float $profondeur,
        public ?SecteurMasque $secteur,
    ) {}

    public static function from(Masque $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            configuration: $data->configuration(),
            orientation: $data->orientation(),
            hauteur: $data->hauteur(),
            profondeur: $data->profondeur(),
            secteur: $data->secteur(),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(MasqueCollection $data): array
    {
        return $data->map(fn(Masque $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'configuration' => $this->configuration->value,
            'orientation' => $this->orientation?->value,
            'hauteur' => $this->hauteur,
            'profondeur' => $this->profondeur,
            'secteur' => $this->secteur?->value,
        ];
    }
}
