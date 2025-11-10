<?php

namespace App\Dto\Audit;

use App\Domain\Audit\Audit;
use App\Dto\Batiment\BatimentDto;
use App\Dto\Logement\LogementDto;
use App\Dto\Scenario\ScenarioDto;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/audit.yaml
 * 
 * @property ?array<LogementDto> $logements
 * @property array<ScenarioDto> $scenarios
 */
#[Validation\Audit\AuditValid]
final class AuditDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $diagnostic_id,
        public readonly \DateTimeImmutable $date_visite,
        public readonly \DateTimeImmutable $date_etablissement,
        public readonly ?BatimentDto $batiment,
        public readonly ?array $logements,
        #[Constraints\All([new Constraints\Type(ScenarioDto::class)])]
        #[Constraints\Valid]
        public readonly array $scenarios,
    ) {}

    public static function from(Audit $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            diagnostic_id: (string) $entity->diagnostic()->id(),
            date_visite: $entity->date_visite(),
            date_etablissement: $entity->date_etablissement(),
            batiment: BatimentDto::from($entity->diagnostic()->batiment()),
            logements: $entity->diagnostic()->logements()->map(fn($item) => LogementDto::from($item))->values(),
            scenarios: $entity->scenarios()->map(fn($item) => ScenarioDto::from($item))->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'diagnostic_id' => $this->diagnostic_id,
            'date_visite' => $this->date_visite->format('Y-m-d'),
            'date_etablissement' => $this->date_etablissement->format('Y-m-d'),
            'batiment' => $this->batiment?->__normalize(),
            'logements' => $this->logements ? array_values(array_map(fn($item) => $item->__normalize(), $this->logements)) : null,
            'scenarios' => array_values(array_map(fn($item) => $item->__normalize(), $this->scenarios ?? [])),
        ];
    }
}
