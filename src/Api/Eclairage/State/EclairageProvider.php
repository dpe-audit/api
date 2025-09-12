<?php

namespace App\Api\Eclairage\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Eclairage\GetEclairageHandler;
use App\Api\Eclairage\Model\Eclairage;
use App\Model\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Eclairage|null>
 */
final class EclairageProvider implements ProviderInterface
{
    public function __construct(private readonly GetEclairageHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Eclairage
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Eclairage::from($entity) : null;
    }
}
