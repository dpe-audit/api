<?php

namespace App\Api\Ecs\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Ecs\GetEcsHandler;
use App\Api\Ecs\Model\Ecs;
use App\Model\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Ecs|null>
 */
final class EcsProvider implements ProviderInterface
{
    public function __construct(private readonly GetEcsHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Ecs
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Ecs::from($entity) : null;
    }
}
