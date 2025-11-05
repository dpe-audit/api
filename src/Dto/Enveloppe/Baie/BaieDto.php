<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\{Baie, TypeBaie, TypeFermeture};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/baie.yaml
 * 
 * @property array<string> $masques
 */
final class BaieDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeBaie $type,
        public readonly bool $presence_protection_solaire,
        public readonly TypeFermeture $type_fermeture,
        public readonly ?int $annee_installation,
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $ujn,
        public readonly ?float $sw,
        public readonly PositionDto $position,
        public readonly VitrageDto $vitrage,
        public readonly ?SurvitrageDto $survitrage,
        public readonly ?MenuiserieDto $menuiserie,
        public readonly array $masques,
    ) {}

    public static function from(Baie $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            presence_protection_solaire: $data->presence_protection_solaire(),
            type_fermeture: $data->type_fermeture(),
            annee_installation: $data->annee_installation(),
            ug: $data->ug(),
            uw: $data->uw(),
            ujn: $data->ujn(),
            sw: $data->sw(),
            position: PositionDto::from($data->position()),
            vitrage: VitrageDto::from($data->vitrage()),
            survitrage: $data->survitrage() ? SurvitrageDto::from($data->survitrage()) : null,
            menuiserie: $data->menuiserie() ? MenuiserieDto::from($data->menuiserie()) : null,
            masques: $data->masques()->map(fn($item) => (string) $item->id())->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'presence_protection_solaire' => $this->presence_protection_solaire,
            'type_fermeture' => $this->type_fermeture->value,
            'annee_installation' => $this->annee_installation,
            'ug' => $this->ug,
            'uw' => $this->uw,
            'ujn' => $this->ujn,
            'sw' => $this->sw,
            'position' => $this->position->__normalize(),
            'vitrage' => $this->vitrage->__normalize(),
            'survitrage' => $this->survitrage?->__normalize(),
            'menuiserie' => $this->menuiserie?->__normalize(),
            'masques' => $this->masques,
        ];
    }
}
