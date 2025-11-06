<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableDatabase, XMLTableElement};
use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, Orientation};
use App\Engine\Rules\SollicitationsClimatiques\SollicitationsExterieures;
use App\Engine\Table\SollicitationsClimatiquesTableValeurRepository;

final class XMLSollicitationsClimatiquesTableValeurRepository implements SollicitationsClimatiquesTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    /**
     * @return array<SollicitationsExterieures>
     */
    public function sollicitations_exterieures(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        bool $parois_anciennes_lourdes,
    ): array {
        return $this->db->repository('ext.ext')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('parois_anciennes_lourdes', $parois_anciennes_lourdes)
            ->andCompareTo('altitude', $altitude)
            ->getMany()
            ->map(fn(XMLTableElement $item) => new SollicitationsExterieures(
                mois: Mois::from($item->strval('mois')),
                epv: $item->floatval('epv'),
                e: $item->floatval('e'),
                efr26: $item->floatval('efr26'),
                efr28: $item->floatval('efr28'),
                nref19: $item->floatval('nref19'),
                nref21: $item->floatval('nref21'),
                nref26: $item->floatval('nref26'),
                nref28: $item->floatval('nref28'),
                dh14: $item->floatval('dh14'),
                dh19: $item->floatval('dh19'),
                dh21: $item->floatval('dh21'),
                dh26: $item->floatval('dh26'),
                dh28: $item->floatval('dh28'),
                tefs: $item->floatval('tefs'),
                text: $item->floatval('text'),
                textmoy_clim26: $item->floatval('textmoy_clim26'),
                textmoy_clim28: $item->floatval('textmoy_clim28'),
            ))
            ->values();
    }

    public function tbase(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
    ): ?float {
        return $this->db->repository('ext.tbase')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('altitude', $altitude)
            ->getOne()
            ?->floatval('tbase');
    }

    /**
     * @return array{mois: Mois, c1: float}[]
     */
    public function c1(
        ZoneClimatique $zone_climatique,
        float $inclinaison,
        ?Orientation $orientation,
    ): array {
        return $this->db->repository('ext.c1')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('orientation', $orientation)
            ->andCompareTo('inclinaison', $inclinaison)
            ->getMany()
            ->map(fn(XMLTableElement $item): array => [
                'mois' => Mois::from($item->strval('mois')),
                'c1' => $item->floatval('c1'),
            ])
            ->values();
    }
}
