<?php

namespace App\Api\Enveloppe;

use App\Api\Enveloppe\Handler\{CreateBaieHandler, CreateLncHandler, CreateMurHandler, CreateNiveauHandler, CreatePlancherBasHandler, CreatePlancherHautHandler, CreatePontThermiqueHandler, CreatePorteHandler};
use App\Api\Enveloppe\Model\Enveloppe as Payload;
use App\Model\Enveloppe\Enveloppe;

final class CreateEnveloppeHandler
{
    public function __construct(
        private readonly CreateLncHandler $lnc_handler,
        private readonly CreateNiveauHandler $niveau_handler,
        private readonly CreateBaieHandler $baie_handler,
        private readonly CreateMurHandler $mur_handler,
        private readonly CreatePlancherBasHandler $plancher_bas_handler,
        private readonly CreatePlancherHautHandler $plancher_haut_handler,
        private readonly CreatePorteHandler $porte_handler,
        private readonly CreatePontThermiqueHandler $pont_thermique_handler,
    ) {}

    public function __invoke(Payload $payload): Enveloppe
    {
        $entity = Enveloppe::create(
            exposition: $payload->exposition,
            q4pa_conv: $payload->q4pa_conv,
            presence_brasseurs_air: $payload->presence_brasseurs_air,
        );

        foreach ($payload->locaux_non_chauffes as $local_non_chauffe_payload) {
            $entity->add_local_non_chauffe(
                $this->lnc_handler->__invoke(payload: $local_non_chauffe_payload, entity: $entity)
            );
        }
        foreach ($payload->niveaux as $niveau_payload) {
            $entity->add_niveau(
                $this->niveau_handler->__invoke(payload: $niveau_payload, entity: $entity)
            );
        }
        foreach ($payload->murs as $mur_payload) {
            $entity->add_mur(
                $this->mur_handler->__invoke(payload: $mur_payload, entity: $entity)
            );
        }
        foreach ($payload->planchers_bas as $plancher_bas_payload) {
            $entity->add_plancher_bas(
                $this->plancher_bas_handler->__invoke(payload: $plancher_bas_payload, entity: $entity)
            );
        }
        foreach ($payload->planchers_hauts as $plancher_haut_payload) {
            $entity->add_plancher_haut(
                $this->plancher_haut_handler->__invoke(payload: $plancher_haut_payload, entity: $entity)
            );
        }
        foreach ($payload->baies as $baie_payload) {
            $entity->add_baie(
                $this->baie_handler->__invoke(payload: $baie_payload, entity: $entity)
            );
        }
        foreach ($payload->portes as $porte_payload) {
            $entity->add_porte(
                $this->porte_handler->__invoke(payload: $porte_payload, entity: $entity)
            );
        }
        foreach ($payload->ponts_thermiques as $pont_thermique_payload) {
            $entity->add_pont_thermique(
                $this->pont_thermique_handler->__invoke(payload: $pont_thermique_payload, entity: $entity)
            );
        }

        return $entity;
    }
}
