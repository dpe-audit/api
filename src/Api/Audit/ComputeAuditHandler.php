<?php

namespace App\Api\Audit;

use App\Model\Audit\Audit;
use App\Engine\Performance\Engine;

final class ComputeAuditHandler
{
    public function __construct(private readonly Engine $engine) {}

    public function __invoke(Audit $entity): Audit
    {
        $engine = $this->engine;
        return $engine($entity);
    }
}
