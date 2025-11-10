<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\{Isolation, Materiau, Porte, PorteData};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/porte.yaml
 */
final class PorteDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?Isolation $isolation,
        public readonly ?Materiau $materiau,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
        public readonly ?float $u,
        public readonly PositionDto $position,
        public readonly MenuiserieDto $menuiserie,
        public readonly VitrageDto $vitrage,

        public readonly ?PorteData $data = null,
    ) {}

    public static function from(Porte $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            isolation: $entity->isolation(),
            materiau: $entity->materiau(),
            annee_installation: $entity->annee_installation(),
            u: $entity->u(),
            position: PositionDto::from($entity->position()),
            menuiserie: MenuiserieDto::from($entity->menuiserie()),
            vitrage: VitrageDto::from($entity->vitrage()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'isolation' => $this->isolation?->value,
            'materiau' => $this->materiau?->value,
            'annee_installation' => $this->annee_installation,
            'u' => $this->u,
            'position' => $this->position->__normalize(),
            'menuiserie' => $this->menuiserie->__normalize(),
            'vitrage' => $this->vitrage->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'sdep' => $this->data->sdep,
                'u' => $this->data->u,
                'b' => $this->data->b,
                'dp' => $this->data->dp,
                'performance' => $this->data->performance?->value,
            ];
        }
        return $data;
    }
}
