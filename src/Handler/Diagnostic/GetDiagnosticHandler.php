<?php

namespace App\Handler\Diagnostic;

use App\Domain\Diagnostic\Diagnostic;
use App\Legacy\Repository\DPERepository;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;

final class GetDiagnosticHandler
{
    public function __construct(
        private readonly DPERepository $repository,
        private readonly DiagnosticTransformer $transformer,
        private readonly CreateDiagnosticHandler $handler,
    ) {}

    public function __invoke(string $id): ?Diagnostic
    {
        if (null === $data = $this->repository->find($id)) {
            return null;
        }
        $payload = $this->transformer->__invoke($data);
        return $this->handler->__invoke($payload);
    }
}
