<?php

namespace App\Handler\Enveloppe;

use App\Domain\Enveloppe\Enveloppe;
use App\Dto\Enveloppe\EnveloppeDto;

final class CreateEnveloppeHandler
{
    public function __construct(
        private readonly CreateLocalNonChauffeHandler $local_non_chauffe_handler,
        private readonly CreateNiveauHandler $niveau_handler,
        private readonly CreateMasqueHandler $masque_handler,
        private readonly CreateDoubleFenetreHandler $double_fenetre_handler,
        private readonly CreateBaieHandler $baie_handler,
        private readonly CreateMurHandler $mur_handler,
        private readonly CreatePlancherBasHandler $plancher_bas_handler,
        private readonly CreatePlancherHautHandler $plancher_haut_handler,
        private readonly CreatePorteHandler $porte_handler,
        private readonly CreatePontThermiqueHandler $pont_thermique_handler,
    ) {}

    public function __invoke(EnveloppeDto $payload): Enveloppe
    {
        $entity = Enveloppe::create(
            exposition: $payload->exposition,
            q4pa_conv: $payload->q4pa_conv,
            presence_brasseurs_air: $payload->presence_brasseurs_air,
        );

        foreach ($payload->locaux_non_chauffes as $local_non_chauffe_payload) {
            $entity->add_local_non_chauffe($this->local_non_chauffe_handler->__invoke($local_non_chauffe_payload, $entity));
        }
        foreach ($payload->niveaux as $niveau_payload) {
            $entity->add_niveau($this->niveau_handler->__invoke($niveau_payload, $entity));
        }
        foreach ($payload->doubles_fenetres as $double_fenetre_payload) {
            $entity->add_double_fenetre($this->double_fenetre_handler->__invoke($double_fenetre_payload, $entity));
        }
        foreach ($payload->masques as $masque_payload) {
            $entity->add_masque($this->masque_handler->__invoke($masque_payload, $entity));
        }
        foreach ($payload->murs as $mur_payload) {
            $entity->add_mur($this->mur_handler->__invoke($mur_payload, $entity));
        }
        foreach ($payload->planchers_bas as $plancher_bas_payload) {
            $entity->add_plancher_bas($this->plancher_bas_handler->__invoke($plancher_bas_payload, $entity));
        }
        foreach ($payload->planchers_hauts as $plancher_haut_payload) {
            $entity->add_plancher_haut($this->plancher_haut_handler->__invoke($plancher_haut_payload, $entity));
        }
        foreach ($payload->baies as $baie_payload) {
            $entity->add_baie($this->baie_handler->__invoke($baie_payload, $entity));
        }
        foreach ($payload->portes as $porte_payload) {
            $entity->add_porte($this->porte_handler->__invoke($porte_payload, $entity));
        }
        foreach ($payload->ponts_thermiques as $pont_thermique_payload) {
            $entity->add_pont_thermique($this->pont_thermique_handler->__invoke($pont_thermique_payload, $entity));
        }

        return $entity;
    }
}
