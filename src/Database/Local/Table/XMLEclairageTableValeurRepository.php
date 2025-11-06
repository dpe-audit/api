<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Batiment\ZoneClimatique;
use App\Engine\Table\EclairageTableValeurRepository;

final class XMLEclairageTableValeurRepository implements EclairageTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function nhecl(ZoneClimatique $zone_climatique): ?float
    {
        return $this->db->repository('eclairage.nhecl')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->getOne()
            ?->floatval('nhecl');
    }
}
