<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Handler\Diagnostic\ComputeDiagnosticHandler;
use App\Handler\Diagnostic\GetDiagnosticHandler;

final class DiagnosticItemProvider implements ProviderInterface
{
    public function __construct(
        private GetDiagnosticHandler $handler,
        private ComputeDiagnosticHandler $computeHandler,
        private ValidatorInterface $validator,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        $entity = $id ? $this->handler->__invoke($id) : null;

        if (null === $entity) {
            return null;
        }

        $payload = DiagnosticDto::from($entity);
        $this->validator->validate($payload);

        return $this->computeHandler->__invoke($payload);
    }
}
