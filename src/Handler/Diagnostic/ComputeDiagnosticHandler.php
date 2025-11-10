<?php

namespace App\Handler\Diagnostic;

use App\Domain\Diagnostic\Diagnostic;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Engine\{Engine, Input};

final class ComputeDiagnosticHandler
{
    public function __construct(
        private readonly CreateDiagnosticHandler $createHandler,
        private readonly Engine $engine,
    ) {}

    public function __invoke(DiagnosticDto $payload): Diagnostic
    {
        $entity = $this->createHandler->__invoke($payload);
        $input = Input::from_diagnostic($entity);
        return $this->engine->__invoke($entity, $input);
    }
}
