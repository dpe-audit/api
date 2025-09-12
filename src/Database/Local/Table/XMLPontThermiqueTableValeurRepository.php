<?php

namespace App\Database\Local\Table;

use App\Domain\Enveloppe\PontThermique\Liaison\{TypeIsolation, TypeLiaison, TypePose};
use App\Database\Local\XMLTableDatabase;
use App\Engine\Table\PontThermiqueTableValeurRepository;

final class XMLPontThermiqueTableValeurRepository implements PontThermiqueTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function kpt(
        TypeLiaison $type_liaison,
        bool $isolation_mur,
        ?bool $isolation_plancher,
        TypeIsolation $type_isolation_mur,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose,
        ?bool $presence_retour_isolation,
        ?float $largeur_dormant,
    ): float {
        return $this->db->repository('pont_thermique.kpt')
            ->createQuery()
            ->and('type_liaison', $type_liaison)
            ->and('isolation_mur', $isolation_mur)
            ->and('type_isolation_mur', $type_isolation_mur)
            ->and('isolation_plancher', $isolation_plancher)
            ->and('type_isolation_plancher', $type_isolation_plancher)
            ->and('type_pose_ouverture', $type_pose)
            ->and('presence_retour_isolation', $presence_retour_isolation)
            ->and('largeur_dormant', $largeur_dormant)
            ->getOne()
            ?->floatval('kpt');
    }
}
