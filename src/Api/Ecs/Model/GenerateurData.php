<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\Perte;
use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Ecs\Entity\Generateur as Entity;

/**
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class GenerateurData
{
    public function __construct(
        public ?float $pecs,
        public ?float $paux,
        public ?float $pn,
        public ?float $cop,
        public ?float $rpn,
        public ?float $qp0,
        public ?float $pveilleuse,
        /** @var Perte[] */
        public array $pertes,
        /** @var Perte[] */
        public array $pertes_recuperables,
        /** @var Consommation[] */
        public array $consommations,
        /** @var Emission[] */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            pecs: $entity->data()->pecs,
            paux: $entity->data()->paux,
            pn: $entity->data()->pn,
            cop: $entity->data()->cop,
            rpn: $entity->data()->rpn?->value(),
            qp0: $entity->data()->qp0,
            pveilleuse: $entity->data()->pveilleuse,
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
