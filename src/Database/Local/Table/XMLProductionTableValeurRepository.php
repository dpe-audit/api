<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Engine\Table\ProductionTableValeurRepository;

final class XMLProductionTableValeurRepository implements ProductionTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function kpv(float $orientation, float $inclinaison): ?float
    {
        return $this->db->repository('production.kpv')
            ->createQuery()
            ->andCompareTo('orientation_pv', $orientation)
            ->andCompareTo('inclinaison_pv', $inclinaison)
            ->getOne()
            ?->floatval('kpv');
    }
}
