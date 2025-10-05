<?php

namespace App\Handler\Chauffage;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Generateur\Position\Position;
use App\Domain\Chauffage\Generateur\Signaletique\Signaletique;
use App\Domain\Reseau\ReseauRepository;
use App\Dto\Chauffage\Generateur\GenerateurDto;

final class CreateGenerateurHandler
{
    public function __construct(private readonly ReseauRepository $reseau_repository) {}

    public function __invoke(GenerateurDto $payload, Chauffage $aggregate): Generateur
    {
        return Generateur::create(
            id: Id::fromString($payload->id),
            chauffage: $aggregate,
            description: $payload->description,
            type: $payload->type,
            energie: $payload->energie,
            bienergie: $payload->bienergie,
            annee_installation: $payload->annee_installation,
            position: Position::create(
                generateur_collectif: $payload->position->generateur_collectif,
                generateur_multi_batiment: $payload->position->generateur_multi_batiment,
                position_volume_chauffe: $payload->position->position_volume_chauffe,
                position_chaudiere: $payload->position->position_chaudiere,
                cascade: $payload->position->cascade,
                priorite_cascade: $payload->position->priorite_cascade,
                generateur_mixte_id: $payload->position->generateur_mixte_id
                    ? Id::fromString($payload->position->generateur_mixte_id)
                    : null,
                reseau_chaleur: $payload->position->reseau_chaleur_id
                    ? $this->reseau_repository->find($payload->position->reseau_chaleur_id)
                    : null,
            ),
            signaletique: Signaletique::create(
                pn: $payload->signaletique->pn,
                label: $payload->signaletique->label,
                scop: $payload->signaletique->scop,
                mode_combustion: $payload->signaletique->mode_combustion,
                presence_ventouse: $payload->signaletique->presence_ventouse,
                presence_regulation_combustion: $payload->signaletique->presence_regulation_combustion,
                pveilleuse: $payload->signaletique->pveilleuse,
                qp0: $payload->signaletique->qp0,
                rpn: $payload->signaletique->rpn,
                rpint: $payload->signaletique->rpint,
                tfonc30: $payload->signaletique->tfonc30,
                tfonc100: $payload->signaletique->tfonc100,
            ),
        );
    }
}
