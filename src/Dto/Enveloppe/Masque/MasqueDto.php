<?php

namespace App\Dto\Enveloppe\Masque;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{ConfigurationMasque, Masque, SecteurMasque, TypeMasque};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/masque.yaml
 */
final class MasqueDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeMasque $type,
        public readonly ConfigurationMasque $configuration,
        public readonly ?Orientation $orientation,
        public readonly ?float $hauteur,
        public readonly ?float $profondeur,
        public readonly ?SecteurMasque $secteur,
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
