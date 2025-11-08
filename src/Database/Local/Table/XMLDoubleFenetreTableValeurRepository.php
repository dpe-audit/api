<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableElement, XMLTableDatabase};
use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\NatureGazLame;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\TypeVitrage;
use App\Engine\Table\DoubleFenetreTableValeurRepository;
use App\Utils\Interpolation;

final class XMLDoubleFenetreTableValeurRepository implements DoubleFenetreTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $inclinaison_vitrage,
        ?float $epaisseur_lame_air,
    ): ?float {
        return $this->db->repository('baie.ug')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('type_vitrage', $type_vitrage)
            ->and('nature_gaz_lame', $nature_gaz_lame, false)
            ->andCompareTo('inclinaison_vitrage', $inclinaison_vitrage)
            ->andCompareTo('epaisseur_lame_air', $epaisseur_lame_air)
            ->getOne()
            ?->floatval('ug');
    }

    public function uw(
        float $ug,
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique
    ): ?float {
        $points = $this->db->repository('baie.uw')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('presence_soubassement', $presence_soubassement)
            ->and('materiau', $materiau)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->getMany()
            ->points('ug', 'uw');

        $f = new Interpolation($points, Interpolation::METHOD_LINEAIRE);
        return $f->interpolationLineaire($ug);
    }

    public function sw(
        TypeBaie $type_baie,
        ?TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
    ): ?float {
        return $this->db->repository('baie.sw')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('type_pose', $type_pose)
            ->and('presence_soubassement', $presence_soubassement)
            ->and('materiau', $materiau)
            ->and('type_vitrage', $type_vitrage)
            ->and('type_survitrage', $type_survitrage)
            ->getOne()
            ?->to(fn(XMLTableElement $record) => $record->floatval('sw'));
    }
}
