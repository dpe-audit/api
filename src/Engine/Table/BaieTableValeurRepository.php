<?php

namespace App\Engine\Table;

use App\Domain\Enveloppe\Baie\{TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\Mitoyennete;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};

interface BaieTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;

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

    public function deltar(TypeFermeture $type_fermeture): ?float;

    public function ujn(float $deltar, float $uw): ?float;

    public function sw(
        TypeBaie $type_baie,
        ?TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
    ): ?float;
}
