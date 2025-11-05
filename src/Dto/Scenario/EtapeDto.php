<?php

namespace App\Dto\Scenario;

use App\Domain\Scenario\Etape\Etape;
use App\Dto\Chauffage\ChauffageDto;
use App\Dto\Ecs\EcsDto;
use App\Dto\Enveloppe\EnveloppeDto;
use App\Dto\Production\ProductionDto;
use App\Dto\Refroidissement\RefroidissementDto;
use App\Dto\Ventilation\VentilationDto;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/scenario/etape.yaml
 */
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
        );
    }

    #[Constraints\IsTrue]
    public function is_generateur_mixte_exists(): bool
    {
        foreach ($this->chauffage->generateurs as $generateur) {
            if ($generateur->position->generateur_mixte_id) {
                if (null === array_find($this->ecs->generateurs, fn($item) => $item->id === $generateur->position->generateur_mixte_id)) {
                    return false;
                }
            }
        }
        foreach ($this->ecs->generateurs as $generateur) {
            if ($generateur->position->generateur_mixte_id) {
                if (null === array_find($this->chauffage->generateurs, fn($item) => $item->id === $generateur->position->generateur_mixte_id)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function __normalize(): array
    {
        return [
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
    }
}
