<?php

namespace App\Dto\Enveloppe\Masque;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{ConfigurationMasque, Masque, MasqueData, SecteurMasque, TypeMasque};

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
        public readonly ?MasqueData $data = null,
    ) {}

    public static function from(Masque $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            configuration: $entity->configuration(),
            orientation: $entity->orientation(),
            hauteur: $entity->hauteur(),
            profondeur: $entity->profondeur(),
            secteur: $entity->secteur(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'configuration' => $this->configuration->value,
            'orientation' => $this->orientation?->value,
            'hauteur' => $this->hauteur,
            'profondeur' => $this->profondeur,
            'secteur' => $this->secteur?->value,
        ];
        if ($this->data) {
            $data['data'] = [
                'fe1' => $this->data->fe1,
                'fe2' => $this->data->fe2,
                'omb' => $this->data->omb,
            ];
        }
        return $data;
    }
}
