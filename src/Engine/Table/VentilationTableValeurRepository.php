<?php

namespace App\Engine\Table;

use App\Domain\Ventilation\Generateur\TypeGenerateur;
use App\Domain\Ventilation\Generateur\TypeVmc;
use App\Domain\Ventilation\Installation\TypeVentilation;

interface VentilationTableValeurRepository
{
    public function qvarep_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;

    public function qvasouf_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;

    public function smea_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;

    public function ratio_utilisation(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;

    public function pvent_moy(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;

    public function pvent(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?float;
}
