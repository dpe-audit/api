<?php

namespace App\Engine\Tables;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Chauffage\Emetteur\{TemperatureDistribution, TypeEmission};
use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Chauffage\Installation\Regulation\TypeIntermittence;
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};

interface ChauffageTableValeurRepository
{
    public function i0(
        TypeBatiment $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        bool $comptage_individuel,
    ): ?float;

    public function fch(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
    ): ?float;

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $generateur_multi_batiment,
        bool $presence_ventouse,
        float $pn,
    ): ?float;

    public function pn(
        PositionChaudiere $position_chaudiere,
        int $annee_installation_generateur,
        float $pdim,
    ): ?float;

    public function rd(
        TypeDistribution $type_distribution,
        TemperatureDistribution $temperature_distribution,
        bool $reseau_collectif,
        ?IsolationReseau $isolation_reseau,
    ): ?float;

    public function re(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): ?float;

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        LabelGenerateur $label_generateur,
        int $annee_installation_generateur,
    ): ?float;

    public function rr(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
        bool $reseau_collectif,
        bool $presence_regulation_terminale,
        ?bool $presence_robinet_thermostatique,
    ): ?float;

    public function scop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        int $annee_installation_generateur,
    ): ?float;

    public function rpn(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?float;

    public function rpint(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?float;

    public function qp0(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
        float $e,
        float $f
    ): ?float;

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?float;

    public function tfonc30(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
        int $annee_installation_generateur,
    ): ?float;

    public function tfonc100(
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
    ): ?float;
}
