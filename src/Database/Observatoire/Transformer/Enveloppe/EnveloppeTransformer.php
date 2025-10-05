<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\EnveloppeDto;

final class EnveloppeTransformer
{
    public function __construct(
        private BaieTransformer $baie_transformer,
        private DoubleFenetreTransformer $double_fenetre_transformer,
        private EtsTransformer $ets_transformer,
        private LncTransformer $lnc_transformer,
        private MasqueTransformer $masque_transformer,
        private MurTransformer $mur_transformer,
        private NiveauTransformer $niveau_transformer,
        private PlancherBasTransformer $plancher_bas_transformer,
        private PlancherHautTransformer $plancher_haut_transformer,
        private PontThermiqueTransformer $pont_thermique_transformer,
        private PorteTransformer $porte_transformer,
    ) {}

    public function __invoke(XMLRessource $ressource): EnveloppeDto
    {
        return new EnveloppeDto(
            exposition: $ressource->logement()->enveloppe->exposition($ressource),
            q4pa_conv: $ressource->logement()->enveloppe->q4pa_conv($ressource),
            presence_brasseurs_air: $ressource->logement()->enveloppe->presence_brasseurs_air($ressource),
            baies: $this->baie_transformer->__invoke($ressource),
            doubles_fenetres: $this->double_fenetre_transformer->__invoke($ressource),
            locaux_non_chauffes: [
                ...$this->ets_transformer->__invoke($ressource),
                ...$this->lnc_transformer->__invoke($ressource),
            ],
            masques: $this->masque_transformer->__invoke($ressource),
            murs: $this->mur_transformer->__invoke($ressource),
            niveaux: $this->niveau_transformer->__invoke($ressource),
            planchers_bas: $this->plancher_bas_transformer->__invoke($ressource),
            planchers_hauts: $this->plancher_haut_transformer->__invoke($ressource),
            ponts_thermiques: $this->pont_thermique_transformer->__invoke($ressource),
            portes: $this->porte_transformer->__invoke($ressource),
        );
    }
}
