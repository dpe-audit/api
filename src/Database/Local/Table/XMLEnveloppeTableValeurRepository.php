<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Batiment\TypeBatiment;
use App\Engine\Table\EnveloppeTableValeurRepository;

final class XMLEnveloppeTableValeurRepository implements EnveloppeTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function q4pa_conv(
        TypeBatiment $type_batiment,
        int $annee_construction,
        bool $presence_joints_menuiserie,
        bool $isolation_murs_plafonds,
    ): ?float {
        return $this->db->repository('enveloppe.q4pa_conv')
            ->createQuery()
            ->and('type_batiment', $type_batiment)
            ->and('presence_joints_menuiserie', $presence_joints_menuiserie)
            ->and('isolation_murs_plafonds', $isolation_murs_plafonds)
            ->andCompareTo('annee_construction', $annee_construction)
            ->getOne()
            ?->floatval('q4pa_conv');
    }
}
