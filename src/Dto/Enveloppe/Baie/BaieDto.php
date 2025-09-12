<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Baie;
use App\Domain\Enveloppe\Baie\BaieCollection;
use App\Domain\Enveloppe\Baie\TypeBaie;
use App\Domain\Enveloppe\Baie\TypeFermeture;
use App\Domain\Enveloppe\Masque\Masque;

/**
 * @property array<string> $masques
 */
final class BaieDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeBaie $type,
        public bool $presence_protection_solaire,
        public TypeFermeture $type_fermeture,
        public ?int $annee_installation,
        public ?float $ug,
        public ?float $uw,
        public ?float $ujn,
        public ?float $sw,
        public PositionDto $position,
        public VitrageDto $vitrage,
        public ?SurvitrageDto $survitrage,
        public ?MenuiserieDto $menuiserie,
        public array $masques,
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
            masques: $data->masques()->map(fn (Masque $item) => (string) $item->id())->values(),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(BaieCollection $data): array
    {
        return $data->map(fn(Baie $item) => self::from($item))->values();
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
