<?php

namespace App\Database\Local\Refroidissement;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Refroidissement\Service\RefroidissementTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLRefroidissementTableValeurRepository implements RefroidissementTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function eer(ZoneClimatique $zone_climatique, Annee $annee_installation_generateur): ?float
    {
        return $this->db->repository('refroidissement.eer')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur->value())
            ->getOne()
            ?->floatval('eer');
    }
}
