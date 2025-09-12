<?php

namespace App\Api\Chauffage\Model;

use App\Api\Common\Model\Perte;
use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Chauffage\Entity\Generateur as Entity;

/**
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class GenerateurData
{
    public function __construct(
        public ?float $pch,
        public ?float $pn,
        public ?float $paux,
        public ?float $scop,
        public ?float $rpn,
        public ?float $rpint,
        public ?float $qp0,
        public ?float $pveilleuse,
        public ?float $tfonc30,
        public ?float $tfonc100,
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
            pch: $entity->data()->pch,
            paux: $entity->data()->paux,
            pn: $entity->data()->pn,
            scop: $entity->data()->scop,
            rpn: $entity->data()->rpn?->value(),
            rpint: $entity->data()->rpint?->value(),
            qp0: $entity->data()->qp0,
            pveilleuse: $entity->data()->pveilleuse,
            tfonc30: $entity->data()->tfonc30,
            tfonc100: $entity->data()->tfonc100,
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
