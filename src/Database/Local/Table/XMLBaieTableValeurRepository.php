<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableElement, XMLTableDatabase};
use App\Domain\Enveloppe\Baie\{TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};
use App\Engine\Table\BaieTableValeurRepository;
use App\Utils\Interpolation;

final class XMLBaieTableValeurRepository extends XMLParoiTableValeurRepository implements BaieTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function ug(
        TypeBaie $type_baie,
        TypeVitrage $type_vitrage,
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
        $records = $this->db->repository('baie.uw')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('presence_soubassement', $presence_soubassement)
            ->and('materiau', $materiau, false)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->getMany();

        if (0 === $records->count()) {
            return null;
        }
        if (1 === $records->count()) {
            return $records->first()->floatval('uw');
        }
        $points = $records->points('ug', 'uw');
        $f = new Interpolation($points, Interpolation::METHOD_LINEAIRE);
        return $f->interpolationLineaire($ug);
    }

    public function deltar(TypeFermeture $type_fermeture): ?float
    {
        return $this->db->repository('baie.deltar')
            ->createQuery()
            ->and('type_fermeture', $type_fermeture)
            ->getOne()
            ?->floatval('deltar');
    }

    public function ujn(float $deltar, float $uw): ?float
    {
        $records = $this->db->repository('baie.ujn')
            ->createQuery()
            ->and('deltar', $deltar)
            ->getMany();

        if (0 === $records->count()) {
            return null;
        }
        if (1 === $records->count()) {
            return $records->first()->floatval('ujn');
        }
        $points = $records->points('uw', 'ujn');
        $f = new Interpolation($points, Interpolation::METHOD_LINEAIRE);
        return $f->interpolationLineaire($uw);
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
