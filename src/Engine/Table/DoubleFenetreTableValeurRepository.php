<?php

namespace App\Engine\Table;

use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\NatureGazLame;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\TypeVitrage;

interface DoubleFenetreTableValeurRepository
{
    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $inclinaison_vitrage,
        ?float $epaisseur_lame_air,
    ): ?float;

    public function uw(
        float $ug,
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique,
    ): ?float;

    public function sw(
        TypeBaie $type_baie,
        ?TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
    ): ?float;
}
