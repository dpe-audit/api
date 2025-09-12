<?php

namespace App\Api\Chauffage\Handler;

use App\Api\Chauffage\Model\Generateur as Payload;
use App\Model\Common\ValueObject\{Annee, Id, Pourcentage};
use App\Model\Chauffage\Chauffage;
use App\Model\Chauffage\Entity\Generateur;
use App\Model\Chauffage\Factory\GenerateurFactory;
use App\Model\Chauffage\ValueObject\Generateur\{Combustion, Signaletique};
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property GenerateurFactory[] $factories
 */
final class CreateGenerateurHandler
{
    public function __construct(
        #[AutowireIterator('app.chauffage.generateur.factory')]
        private readonly iterable $factories,
    ) {}

    public function __invoke(Payload $payload, Chauffage $entity): Generateur
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
                : null
        );

        if ($payload->reseau_chaleur_id) {
            $factory->set_reseau_chaleur(Id::from($payload->reseau_chaleur_id));
        }

        return Generateur::create($factory);
    }

    private function factory(Payload $payload, Chauffage $entity): ?GenerateurFactory
    {
        foreach ($this->factories as $factory) {
            if (false === $factory::supports($payload->type, $payload->energie)) {
                continue;
            }
            $factory = $factory->initialize(
                id: Id::from($payload->id),
                chauffage: $entity,
                description: $payload->description,
                type: $payload->type,
                energie: $payload->energie,
                annee_installation: $payload->annee_installation
                    ? Annee::from($payload->annee_installation)
                    : null,
            );

            if ($payload->energie_partie_chaudiere) {
                $factory->set_energie_partie_chaudiere(
                    energie_partie_chaudiere: $payload->energie_partie_chaudiere,
                );
            }

            return $factory;
        }
        return null;
    }

    private function create_signaletique(Payload $payload): Signaletique
    {
        return Signaletique::create(
            type_chaudiere: $payload->type_chaudiere,
            pn: $payload->pn,
            scop: $payload->scop,
            label: $payload->label,
            priorite_cascade: $payload->priorite_cascade,
            combustion: Combustion::create(
                mode_combustion: $payload->mode_combustion,
                presence_ventouse: $payload->presence_ventouse,
                presence_regulation_combustion: $payload->presence_regulation_combustion,
                pveilleuse: $payload->pveilleuse,
                qp0: $payload->qp0,
                rpn: $payload->rpn ? Pourcentage::from($payload->rpn) : null,
                rpint: $payload->rpint ? Pourcentage::from($payload->rpint) : null,
                tfonc30: $payload->tfonc30,
                tfonc100: $payload->tfonc100,
            )
        );
    }
}
