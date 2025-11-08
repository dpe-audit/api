<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Handler\Diagnostic\ComputeDiagnosticHandler;

final class CalculeDiagnosticProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ComputeDiagnosticHandler $handler,
    ) {}

    /**
     * @param DiagnosticDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $entity = $this->handler->__invoke($data);
        return DiagnosticDto::from($entity);
    }
}
