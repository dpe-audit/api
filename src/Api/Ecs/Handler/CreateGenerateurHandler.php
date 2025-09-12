<?php

namespace App\Api\Ecs\Handler;

use App\Api\Ecs\Model\Generateur as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Pourcentage};
use App\Model\Ecs\Ecs;
use App\Model\Ecs\Entity\Generateur;
use App\Model\Ecs\Factory\GenerateurFactory;
use App\Model\Ecs\ValueObject\Generateur\{Combustion, Signaletique};
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property GenerateurFactory[] $factories
 */
final class CreateGenerateurHandler
{
    public function __construct(
        #[AutowireIterator('app.ecs.generateur.factory')]
        private readonly iterable $factories,
    ) {}

    public function __invoke(Payload $payload, Ecs $entity): Generateur
    {
        if (null === $factory = $this->factory($payload, $entity)) {
            throw new \RuntimeException('No factory found for the given payload.');
        }
        $factory->set_signaletique($this->create_signaletique($payload));

        $factory->set_position(
            generateur_collectif: $payload->generateur_collectif,
            position_volume_chauffe: $payload->position_volume_chauffe,
            generateur_multi_batiment: $payload->generateur_multi_batiment,
            generateur_mixte_id: $payload->generateur_mixte_id
                ? Id::from($payload->generateur_mixte_id)
                : null,
        );

        if ($payload->reseau_chaleur_id) {
            $factory->set_reseau_chaleur(Id::from($payload->reseau_chaleur_id));
        }

        return Generateur::create($factory);
    }

    private function factory(Payload $payload, Ecs $entity): ?GenerateurFactory
    {
        foreach ($this->factories as $factory) {
            if (false === $factory::supports($payload->type, $payload->energie)) {
                continue;
            }
            return $factory->initialize(
                id: Id::from($payload->id),
                ecs: $entity,
                description: $payload->description,
                type: $payload->type,
                energie: $payload->energie,
                annee_installation: $payload->annee_installation
                    ? Annee::from($payload->annee_installation)
                    : null,
            );
        }
        return null;
    }

    private function create_signaletique(Payload $payload): Signaletique
    {
        return Signaletique::create(
            volume_stockage: $payload->volume_stockage,
            type_chaudiere: $payload->type_chaudiere,
            label: $payload->label,
            pn: $payload->pn,
            cop: $payload->cop,
            combustion: Combustion::create(
                mode_combustion: $payload->mode_combustion,
                presence_ventouse: $payload->presence_ventouse,
                pveilleuse: $payload->pveilleuse,
                qp0: $payload->qp0,
                rpn: $payload->rpn ?
                    Pourcentage::from($payload->rpn)
                    : null,
            )
        );
    }
}
