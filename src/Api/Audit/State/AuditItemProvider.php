<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Audit\{ComputeAuditHandler, GetAuditHandler};
use App\Api\Audit\Model\Audit;
use App\Model\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Audit|null>
 */
final class AuditItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly GetAuditHandler $get_handler,
        private readonly ComputeAuditHandler $compute_handler,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Audit
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->get_handler)($id) : null;
        $entity = $entity ? ($this->compute_handler)($entity) : null;
        return $entity ? Audit::from($entity) : null;
    }
}
