<?php

namespace App\Dto\Scenario;

use App\Domain\Scenario\Scenario;
use App\Domain\Scenario\TypeScenario;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/scenario/scenario.yaml
 * 
 * @property array<EtapeDto> $etapes
 */
final class ScenarioDto
{
    public function __construct(
        public readonly string $id,
        public readonly TypeScenario $type,
        public readonly string $nom,
        public readonly string $description,

        #[Constraints\All([new Constraints\Type(EtapeDto::class)])]
        #[Constraints\Valid]
        public readonly array $etapes,
    ) {}

    public static function from(Scenario $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            type: $entity->type(),
            nom: $entity->nom(),
            description: $entity->description(),
            etapes: $entity->etapes()->map(fn($item) => EtapeDto::from($item))->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'nom' => $this->nom,
            'description' => $this->description,
            'etapes' => array_map(fn($etape) => $etape->__normalize(), $this->etapes),
        ];
    }
}
