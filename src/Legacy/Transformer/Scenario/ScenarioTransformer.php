<?php

namespace App\Legacy\Transformer\Scenario;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Scenario\TypeScenario;
use App\Dto\Scenario\ScenarioDto;
use App\Legacy\Transformer\Context;

final class ScenarioTransformer
{
    private Context $context;

    public function __construct(private readonly EtapeTransformer $etape_transformer) {}

    public function description_scenario(): string
    {
        return match ($this->type_scenario()) {
            TypeScenario::RENOVATION_GLOBALE => "Scénario de rénovation globale",
            default => "Scénario de rénovation par étapes",
        };
    }

    public function type_scenario(): TypeScenario
    {
        return match ($this->context->logement()->caracteristique_generale->enum_scenario_id) {
            1 => TypeScenario::RENOVATION_GLOBALE,
            default => TypeScenario::RENOVATION_GLOBALE,
        };
    }


    public function __invoke(Context $context): ?ScenarioDto
    {
        if (null === $context->logement()) {
            return null;
        }
        if (null === $context->logement()->caracteristique_generale->enum_scenario_id) {
            return null;
        }
        if (0 === $context->logement()->caracteristique_generale->enum_scenario_id) {
            return null;
        }
        return null;
        /*

        $etapes = [];
        foreach ($audit->logement_collection as $logement) {
            $context->set_logement($logement);
            $etapes[] = $this->etape_transformer->__invoke($context);
        }

        return new ScenarioDto(
            id: (string) Id::create(),
            type: $this->type(),
            nom: $context->logement()->caracteristique_generale->nom_scenario ?? "Scénario",
            description: $this->description_scenario(),
            etapes: array_filter($etapes),
        );*/
    }
}
