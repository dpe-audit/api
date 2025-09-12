<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableElement, XMLTableDatabase};
use App\Domain\Enveloppe\Baie\{TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};
use App\Engine\Table\BaieTableValeurRepository;
use App\Utils\Math;

final class XMLBaieTableValeurRepository extends XMLParoiTableValeurRepository implements BaieTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $inclinaison_vitrage,
        ?float $epaisseur_lame_air,
    ): ?float {
        $records = $this->db->repository('baie.ug')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('type_vitrage', $type_vitrage)
            ->and('nature_gaz_lame', $nature_gaz_lame)
            ->andCompareTo('inclinaison_vitrage', $inclinaison_vitrage)
            ->getMany()
            ->usort('epaisseur_lame_air', $epaisseur_lame_air)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('ug');
        }
        return Math::interpolation_lineaire(
            x: $epaisseur_lame_air,
            x1: $records->first()->floatval('epaisseur_lame_air'),
            x2: $records->last()->floatval('epaisseur_lame_air'),
            y1: $records->first()->floatval('ug'),
            y2: $records->last()->floatval('ug')
        );
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
            ->and('materiau', $materiau)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->getMany()
            ->usort('ug', $ug)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('uw');
        }
        return Math::interpolation_lineaire(
            x: $ug,
            x1: $records->first()->floatval('ug'),
            x2: $records->last()->floatval('ug'),
            y1: $records->first()->floatval('uw'),
            y2: $records->last()->floatval('uw')
        );
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
            ->getMany()
            ->usort('uw', $uw)
            ->slice(0, 2);

        if ($records->count() === 0) {
            return null;
        }
        if ($records->count() === 1) {
            return $records->first()->floatval('ujn');
        }
        return Math::interpolation_lineaire(
            x: $uw,
            x1: $records->first()->floatval('uw'),
            x2: $records->last()->floatval('uw'),
            y1: $records->first()->floatval('ujn'),
            y2: $records->last()->floatval('ujn')
        );
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
