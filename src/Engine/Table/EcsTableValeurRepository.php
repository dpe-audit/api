<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\ModeCombustion;
use App\Domain\Ecs\Generateur\TypeGenerateur;
use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;

interface EcsTableValeurRepository
{
    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $presence_ventouse,
        float $pn,
    ): ?float;

    public function rd(
        bool $production_volume_habitable,
        bool $reseau_collectif,
        bool $alimentation_contigue,
        ?BouclageReseau $bouclage_reseau,
    ): ?float;

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
    ): ?float;

    public function cr(
        TypeGenerateur $type_generateur,
        float $volume_stockage,
        ?LabelGenerateur $label_generateur,
    ): ?float;

    public function cop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        int $annee_installation,
    ): ?float;

    public function fecs(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        Usage $usage_solaire,
        int $annee_installation,
    ): ?float;

    public function rpn(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation,
        float $pn,
    ): ?float;

    public function qp0(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation,
        float $pn,
        float $e,
        float $f,
    ): ?float;

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation,
    ): ?float;
}
