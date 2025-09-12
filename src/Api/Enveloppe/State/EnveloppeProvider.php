<?php

namespace App\Api\Enveloppe\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Enveloppe\GetEnveloppeHandler;
use App\Api\Enveloppe\Model\Enveloppe;
use App\Model\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Enveloppe|null>
 */
final class EnveloppeProvider implements ProviderInterface
{
    public function __construct(private readonly GetEnveloppeHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Enveloppe
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Enveloppe::from($entity) : null;
    }
}
