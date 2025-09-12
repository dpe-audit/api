<?php

namespace App\Database\Local\Table;

use App\Domain\Adresse\ZoneClimatique;
use App\Database\Local\XMLTableDatabase;
use App\Engine\Table\RefroidissementTableValeurRepository;

final class XMLRefroidissementTableValeurRepository implements RefroidissementTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function eer(ZoneClimatique $zone_climatique, int $annee_installation_generateur): ?float
    {
        return $this->db->repository('refroidissement.eer')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->floatval('eer');
    }
}
