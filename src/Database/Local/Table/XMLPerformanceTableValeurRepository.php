<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Adresse\ZoneClimatique;
use App\Domain\Ressource\{EtiquetteClimat, EtiquetteEnergie};
use App\Engine\Table\PerformanceTableValeurRepository;

final class XMLPerformanceTableValeurRepository implements PerformanceTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function etiquette_energie(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $cep,
        float $eges,
    ): ?EtiquetteEnergie {
        $value = $this->db->repository('performance.etiquette_energie')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->andCompareTo('altitude', $altitude)
            ->andCompareTo('cep', $cep)
            ->andCompareTo('eges', $eges)
            ->getOne()
            ?->strval('etiquette');

        return $value ? EtiquetteEnergie::from($value) : null;
    }

    public function etiquette_climat(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $eges,
    ): ?EtiquetteClimat {
        $value = $this->db->repository('performance.etiquette_climat')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->andCompareTo('altitude', $altitude)
            ->andCompareTo('eges', $eges)
            ->getOne()
            ?->strval('etiquette');

        return $value ? EtiquetteClimat::from($value) : null;
    }
}
