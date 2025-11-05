<?php

namespace App\Handler\Audit;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Diagnostic\DiagnosticRepository;
use App\Dto\Audit\AuditDto;
use App\Handler\Scenario\CreateScenarioHandler;
use Webmozart\Assert\Assert;

final class CreateAuditHandler
{
    public function __construct(
        private readonly CreateScenarioHandler $createScenarioHandler,
        private readonly DiagnosticRepository $diagnosticRepository
    ) {}

    public function __invoke(AuditDto $payload): Audit
    {
        $aggregate = $this->diagnosticRepository->find(Id::fromString($payload->diagnostic_id));
        Assert::notNull($aggregate);

        $entity = Audit::create(
            diagnostic: $aggregate,
            date_visite: $payload->date_visite,
            date_etablissement: $payload->date_etablissement,
        );

        foreach ($payload->scenarios as $item) {
            $entity->add_scenario($this->createScenarioHandler->__invoke($item, $entity));
        }
        return $entity;
    }
}
