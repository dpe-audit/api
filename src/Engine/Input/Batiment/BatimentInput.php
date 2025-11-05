<?php

namespace App\Engine\Input\Batiment;

use App\Engine\{Engine, Input};
use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Enum\Mois;
use App\Engine\Rules\SollicitationsClimatiques\SollicitationsExterieuresRule;
use App\Engine\Rules\ZoneThermique\ZoneThermiqueRule;

final class BatimentInput extends Input
{
    public function __construct(
        public readonly Batiment $entity,
        public readonly Engine $context,
    ) {}

    public function code_departement(): string
    {
        return $this->context->ressource()->adresse()->code_departement;
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->context->ressource()->batiment()->type;
    }

    public function annee_construction(): int
    {
        return $this->context->ressource()->batiment()->annee_construction;
    }

    public function altitude(): int
    {
        return $this->context->ressource()->batiment()->altitude;
    }

    public function materiaux_anciens(): bool
    {
        return $this->context->ressource()->batiment()->materiaux_anciens;
    }

    public function surface_chauffee(): float
    {
        return $this->context->ressource()->chauffage()->installations()->surface();
    }

    public function surface_chauffee_effet_joule(): float
    {
        return $this->context->ressource()->chauffage()->installations()->with_effet_joule()->surface();
    }

    public function surface_habitable_logement(): float
    {
        return $this->context->ressource()->logements()->first()->surface_habitable();
    }

    public function surface_habitable_batiment(): float
    {
        return $this->context->ressource()->batiment()->surface_habitable;
    }

    public function hauteur_sous_plafond_logement(): float
    {
        return $this->context->ressource()->logements()->first()->hauteur_sous_plafond();
    }

    public function hauteur_sous_plafond_batiment(): float
    {
        return $this->context->ressource()->batiment()->hauteur_sous_plafond;
    }

    public function logements(): int
    {
        return $this->context->ressource()->batiment()->logements;
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->surface_habitable() / $this->logements();
    }

    // * Données calculées

    public function zone_thermique_rule(): ZoneThermiqueRule
    {
        return $this->require(ZoneThermiqueRule::class);
    }

    public function sollicitations_exterieures_rule(): SollicitationsExterieuresRule
    {
        return $this->require(SollicitationsExterieuresRule::class);
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->sollicitations_exterieures_rule()->zone_climatique();
    }

    public function effet_joule(): bool
    {
        return $this->zone_thermique_rule()->effet_joule();
    }

    public function surface_habitable(): float
    {
        return $this->zone_thermique_rule()->surface_reference();
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->zone_thermique_rule()->hauteur_sous_plafond();
    }

    public function volume_habitable(): float
    {
        return $this->zone_thermique_rule()->volume_reference();
    }

    public function tbase(): float
    {
        return $this->sollicitations_exterieures_rule()->tbase();
    }

    public function e(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->e($mois);
    }

    public function e_fr(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->e_fr(mois: $mois);
    }

    public function dh(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->dh($mois);
    }

    public function dh14(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->dh14($mois);
    }

    public function nref(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->nref($mois);
    }

    public function nref_fr(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->nref_fr($mois);
    }

    public function tefs(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->tefs($mois);
    }

    public function epv(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->epv($mois);
    }

    public function text(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->text($mois);
    }

    public function text_fr(Mois $mois): float
    {
        return $this->sollicitations_exterieures_rule()->text_fr($mois);
    }
}
