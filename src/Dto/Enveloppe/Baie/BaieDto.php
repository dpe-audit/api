<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\{Baie, BaieData, TypeBaie, TypeFermeture};
use App\Validation;

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
        #[Validation\Annee\AnneeValid]
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

        public readonly ?BaieData $data = null,
    ) {}

    public static function from(Baie $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            presence_protection_solaire: $entity->presence_protection_solaire(),
            type_fermeture: $entity->type_fermeture(),
            annee_installation: $entity->annee_installation(),
            ug: $entity->ug(),
            uw: $entity->uw(),
            ujn: $entity->ujn(),
            sw: $entity->sw(),
            position: PositionDto::from($entity->position()),
            vitrage: VitrageDto::from($entity->vitrage()),
            survitrage: $entity->survitrage() ? SurvitrageDto::from($entity->survitrage()) : null,
            menuiserie: $entity->menuiserie() ? MenuiserieDto::from($entity->menuiserie()) : null,
            masques: $entity->masques()->map(fn($item) => (string) $item->id())->values(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
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
            'masques' => array_values($this->masques),
        ];
        if ($this->data) {
            $data['data'] = [
                'sdep' => $this->data->sdep,
                'ug' => $this->data->ug,
                'uw' => $this->data->uw,
                'deltar' => $this->data->deltar,
                'b' => $this->data->b,
                'u' => $this->data->u,
                'dp' => $this->data->dp,
                'performance' => $this->data->performance?->value,
                'fe' => $this->data->fe,
                'sw' => $this->data->sw,
                'sse' => $this->data->sse,
            ];
        }
        return $data;
    }
}
