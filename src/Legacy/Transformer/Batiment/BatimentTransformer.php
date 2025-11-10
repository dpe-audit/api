<?php

namespace App\Legacy\Transformer\Batiment;

use App\Domain\Batiment\{ClasseAltitude, PeriodeConstruction, TypeBatiment};
use App\Dto\Batiment\BatimentDto;
use App\Legacy\Transformer\Adresse\AdresseTransformer;
use App\Legacy\Transformer\Context;

final class BatimentTransformer
{
    private Context $context;

    public function __construct(private readonly AdresseTransformer $adresse_transformer) {}

    public function type_batiment(): TypeBatiment
    {
        return match ($this->context->ressource()->logement()->caracteristique_generale->enum_methode_application_dpe_log_id) {
            1, 14, 18 => TypeBatiment::MAISON,
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 19,
            20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33,
            34, 35, 36, 37, 38, 39, 40 => TypeBatiment::IMMEUBLE,
        };
    }

    public function annee_construction(): int
    {
        return $this->context->ressource()->logement()->caracteristique_generale->annee_construction ?? $this->periode_construction()->intval();
    }

    public function periode_construction(): PeriodeConstruction
    {
        return match ($this->context->ressource()->logement()->caracteristique_generale->enum_periode_construction_id) {
            1 => PeriodeConstruction::AVANT_1948,
            2 => PeriodeConstruction::ENTRE_1948_1974,
            3 => PeriodeConstruction::ENTRE_1975_1977,
            4 => PeriodeConstruction::ENTRE_1978_1982,
            5 => PeriodeConstruction::ENTRE_1983_1988,
            6 => PeriodeConstruction::ENTRE_1989_2000,
            7 => PeriodeConstruction::ENTRE_2001_2005,
            8 => PeriodeConstruction::ENTRE_2006_2012,
            9 => PeriodeConstruction::ENTRE_2013_2021,
            10 => PeriodeConstruction::APRES_2021,
        };
    }

    public function surface_habitable(): float
    {
        return $this->context->ressource()->logement()->caracteristique_generale->surface_habitable_immeuble
            ?? $this->context->ressource()->logement()->caracteristique_generale->surface_habitable_logement;
    }

    public function logements(): int
    {
        return ($value = $this->context->ressource()->logement()->caracteristique_generale->nombre_appartement) ? $value : 1;
    }

    public function altitude(): int
    {
        return $this->context->ressource()->logement()->meteo->altitude ?? $this->classe_altitude()->altitude();
    }

    public function classe_altitude(): ClasseAltitude
    {
        return match ($this->context->ressource()->logement()->meteo->enum_classe_altitude_id) {
            1 => ClasseAltitude::_400_LT,
            2 => ClasseAltitude::_400_800,
            3 => ClasseAltitude::_800_GT,
        };
    }

    public function __invoke(Context $context): BatimentDto
    {
        $this->context = $context;

        return new BatimentDto(
            rnb_id: $context->ressource()->administratif()->geolocalisation->id_batiment_rnb,
            type: $this->type_batiment(),
            annee_construction: $this->annee_construction(),
            logements: $this->logements(),
            surface_habitable: $this->surface_habitable(),
            hauteur_sous_plafond: $context->ressource()->logement()->caracteristique_generale->hsp,
            altitude: $this->altitude(),
            materiaux_anciens: $context->ressource()->logement()->meteo->batiment_materiaux_anciens,
            adresse: $this->adresse_transformer->__invoke($context->ressource()->administratif()->geolocalisation->adresses->adresse_bien)
        );
    }
}
