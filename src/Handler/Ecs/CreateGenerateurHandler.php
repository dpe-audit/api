<?php

namespace App\Handler\Ecs;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Generateur\Position\Position;
use App\Domain\Ecs\Generateur\Signaletique\Signaletique;
use App\Domain\Reseau\ReseauRepository;
use App\Dto\Ecs\Generateur\GenerateurDto;

final class CreateGenerateurHandler
{
    public function __construct(private readonly ReseauRepository $reseau_repository) {}

    public function __invoke(GenerateurDto $payload, Ecs $aggregate): Generateur
    {
        return Generateur::create(
            id: Id::fromString($payload->id),
            ecs: $aggregate,
            description: $payload->description,
            type: $payload->type,
            energie: $payload->energie,
            annee_installation: $payload->annee_installation,
            position: Position::create(
                generateur_collectif: $payload->position->generateur_collectif,
                generateur_multi_batiment: $payload->position->generateur_multi_batiment,
                position_volume_chauffe: $payload->position->position_volume_chauffe,
                position_chauffe_eau: $payload->position->position_chauffe_eau,
                generateur_mixte_id: $payload->position->generateur_mixte_id
                    ? Id::fromString($payload->position->generateur_mixte_id)
                    : null,
                reseau_chaleur: $payload->position->reseau_chaleur_id
                    ? $this->reseau_repository->find($payload->position->reseau_chaleur_id)
                    : null,
            ),
            signaletique: Signaletique::create(
                volume_stockage: $payload->signaletique->volume_stockage,
                pn: $payload->signaletique->pn,
                label: $payload->signaletique->label,
                cop: $payload->signaletique->cop,
                mode_combustion: $payload->signaletique->mode_combustion,
                presence_ventouse: $payload->signaletique->presence_ventouse,
                pveilleuse: $payload->signaletique->pveilleuse,
                qp0: $payload->signaletique->qp0,
                rpn: $payload->signaletique->rpn,
            ),
        );
    }
}
