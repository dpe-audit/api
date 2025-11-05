<?php

namespace App\Dto\Diagnostic;

use App\Domain\Diagnostic\Diagnostic;
use App\Dto\Batiment\BatimentDto;
use App\Dto\Chauffage\ChauffageDto;
use App\Dto\Ecs\EcsDto;
use App\Dto\Enveloppe\EnveloppeDto;
use App\Dto\Logement\LogementDto;
use App\Dto\Production\ProductionDto;
use App\Dto\Refroidissement\RefroidissementDto;
use App\Dto\Ventilation\VentilationDto;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/diagnostic.yaml
 * 
 * @property array<LogementDto> $logements
 */
final class DiagnosticDto
{
    public function __construct(
        public readonly ?string $id,
        public readonly \DateTimeImmutable $date_visite,
        public readonly \DateTimeImmutable $date_etablissement,
        #[Constraints\Valid]
        public readonly BatimentDto $batiment,
        #[Constraints\Valid]
        public readonly EnveloppeDto $enveloppe,
        #[Constraints\Valid]
        public readonly ChauffageDto $chauffage,
        #[Constraints\Valid]
        public readonly EcsDto $ecs,
        #[Constraints\Valid]
        public readonly RefroidissementDto $refroidissement,
        #[Constraints\Valid]
        public readonly VentilationDto $ventilation,
        #[Constraints\Valid]
        public readonly ProductionDto $production,
        #[Constraints\All([new Constraints\Type(LogementDto::class)])]
        #[Constraints\Valid]
        public readonly array $logements,
    ) {}

    public static function from(Diagnostic $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            date_visite: $entity->date_visite(),
            date_etablissement: $entity->date_etablissement(),
            batiment: BatimentDto::from($entity->batiment()),
            enveloppe: EnveloppeDto::from($entity->enveloppe()),
            chauffage: ChauffageDto::from($entity->chauffage()),
            ecs: EcsDto::from($entity->ecs()),
            refroidissement: RefroidissementDto::from($entity->refroidissement()),
            ventilation: VentilationDto::from($entity->ventilation()),
            production: ProductionDto::from($entity->production()),
            logements: $entity->logements()->map(fn($logement) => LogementDto::from($logement))->values(),
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
            'date_visite' => $this->date_visite->format('Y-m-d'),
            'date_etablissement' => $this->date_etablissement->format('Y-m-d'),
            'batiment' => $this->batiment->__normalize(),
            'enveloppe' => $this->enveloppe->__normalize(),
            'chauffage' => $this->chauffage->__normalize(),
            'ecs' => $this->ecs->__normalize(),
            'refroidissement' => $this->refroidissement->__normalize(),
            'ventilation' => $this->ventilation->__normalize(),
            'production' => $this->production->__normalize(),
            'logements' => array_map(fn($logement) => $logement->__normalize(), $this->logements),
        ];
    }
}
