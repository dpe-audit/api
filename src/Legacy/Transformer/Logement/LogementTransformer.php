<?php

namespace App\Legacy\Transformer\Logement;

use App\Domain\Logement\{Position, Typologie};
use App\Dto\Logement\LogementDto;
use App\Legacy\Model\LogementVisite;
use App\Legacy\Transformer\Context;

final class LogementTransformer
{
    private LogementVisite $logement_visite;

    public function position(): Position
    {
        return match ($this->logement_visite->enum_position_etage_logement_id) {
            1 => Position::RDC,
            2 => Position::ETAGE_INTERMEDIAIRE,
            3 => Position::DERNIER_ETAGE,
        };
    }

    public function typologie(): Typologie
    {
        return match ($this->logement_visite->enum_typologie_logement_id) {
            1 => Typologie::T1,
            2 => Typologie::T2,
            3 => Typologie::T3,
            4 => Typologie::T4,
            5 => Typologie::T5,
            6 => Typologie::T6,
            7 => Typologie::T7,
        };
    }

    public function __invoke(LogementVisite $logement_visite, Context $context): LogementDto
    {
        $this->logement_visite = $logement_visite;

        return new LogementDto(
            id: $logement_visite->id(),
            description: $logement_visite->description,
            surface_habitable: $logement_visite->surface_habitable_logement,
            hauteur_sous_plafond: $context->ressource()->logement()->caracteristique_generale->hsp,
            position: $this->position(),
            typologie: $this->typologie(),
        );
    }
}
