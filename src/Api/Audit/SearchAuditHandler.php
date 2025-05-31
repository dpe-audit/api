<?php

namespace App\Api\Audit;

use App\Api\Audit\Query\SearchQuery;
use App\Domain\Audit\{AuditCollection, AuditRepository};

final class SearchAuditHandler
{
    private ?int $count = null;

    public function __construct(private readonly AuditRepository $repository) {}

    public function count(): ?int
    {
        return $this->count;
    }

    public function __invoke(SearchQuery $query): AuditCollection
    {
        $repository = $this->repository
            ->with_date_etablissement(min: $query->date_etablissement_min, max: $query->date_etablissement_max)
            ->with_surface_habitable(min: $query->surface_habitable_min, max: $query->surface_habitable_max)
            ->with_annee_construction(min: $query->annee_construction_min, max: $query->annee_construction_max)
            ->with_altitude(min: $query->altitude_min, max: $query->altitude_max)
            ->with_type_batiment(...$query->type_batiment)
            ->with_periode_construction(...$query->periode_constrcution)
            ->with_classe_altitude(...$query->classe_altitude)
            ->with_etiquette_energie(...$query->etiquette_energie)
            ->with_etiquette_climat(...$query->etiquette_climat)
            ->with_code_departement(...$query->code_departement)
            ->with_code_postal(...$query->code_postal)
            ->with_zone_climatique(...$query->zone_climatique)
            ->with_adresse($query->ban_id)
            ->sort($query->sort ?? 'numero_dpe');

        $collection = $repository->search(page: $query->page ?? 1, randomize: $query->randomize ?? false);
        $this->count = $repository->count();

        return $collection;
    }
}
