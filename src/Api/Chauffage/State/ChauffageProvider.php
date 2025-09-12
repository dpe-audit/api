<?php

namespace App\Api\Chauffage\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Chauffage\GetChauffageHandler;
use App\Api\Chauffage\Model\Chauffage;
use App\Model\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Chauffage|null>
 */
final class ChauffageProvider implements ProviderInterface
{
    public function __construct(private readonly GetChauffageHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Chauffage
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Chauffage::from($entity) : null;
    }
}
