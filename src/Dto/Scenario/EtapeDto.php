<?php

namespace App\Dto\Scenario;

use App\Domain\Scenario\Etape\{Etape, EtapeData};
use App\Dto\Chauffage\ChauffageDto;
use App\Dto\Ecs\EcsDto;
use App\Dto\Enveloppe\EnveloppeDto;
use App\Dto\Production\ProductionDto;
use App\Dto\Refroidissement\RefroidissementDto;
use App\Dto\Ventilation\VentilationDto;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/scenario/etape.yaml
 */
#[Validation\Scenario\EtapeValid]
final class EtapeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $nom,
        public readonly string $description,
        #[Constraints\Valid]
        public readonly EnveloppeDto $enveloppe,
        #[Constraints\Valid]
        public readonly ChauffageDto $chauffage,
        #[Constraints\Valid]
        public readonly EcsDto $ecs,
        #[Constraints\Valid]
        public readonly VentilationDto $ventilation,
        #[Constraints\Valid]
        public readonly RefroidissementDto $refroidissement,
        #[Constraints\Valid]
        public readonly ProductionDto $production,

        public readonly ?EtapeData $data = null,
    ) {}

    public static function from(Etape $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            nom: $entity->nom(),
            description: $entity->description(),
            enveloppe: EnveloppeDto::from($entity->enveloppe()),
            chauffage: ChauffageDto::from($entity->chauffage()),
            ecs: EcsDto::from($entity->ecs()),
            ventilation: VentilationDto::from($entity->ventilation()),
            refroidissement: RefroidissementDto::from($entity->refroidissement()),
            production: ProductionDto::from($entity->production()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'enveloppe' => $this->enveloppe->__normalize(),
            'chauffage' => $this->chauffage->__normalize(),
            'ecs' => $this->ecs->__normalize(),
            'ventilation' => $this->ventilation->__normalize(),
            'refroidissement' => $this->refroidissement->__normalize(),
            'production' => $this->production->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'zone_climatique' => $this->data->zone_climatique?->value,
                'effet_joule' => $this->data->effet_joule,
                'parois_anciennes_lourdes' => $this->data->parois_anciennes_lourdes,
                'surface_reference' => $this->data->surface_reference,
                'volume_reference' => $this->data->volume_reference,
                'bilan' => [
                    'cef' => $this->data->bilan?->cef,
                    'cep' => $this->data->bilan?->cep,
                    'eges' => $this->data->bilan?->eges,
                    'etiquette_energie' => $this->data->bilan?->etiquette_energie?->value,
                    'etiquette_climat' => $this->data->bilan?->etiquette_climat?->value,
                ],
                'consommations' => $this->data->consommations?->__normalize(),
            ];
        }
        return $data;
    }
}
