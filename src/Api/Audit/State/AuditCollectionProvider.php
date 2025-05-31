<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\{PaginatorInterface, Pagination};
use App\Api\Audit\Collection\{Audit, AuditPaginator};
use App\Api\Audit\SearchAuditHandler;
use App\Api\Audit\Query\SearchQuery;
use App\Domain\Audit\Enum\{Etiquette, ClasseAltitude, PeriodeConstruction, TypeBatiment};
use App\Domain\Common\Enum\ZoneClimatique;

/**
 * @implements ProviderInterface<Audit|null>
 */
final class AuditCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly SearchAuditHandler $handler,
        private readonly Pagination $pagination,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): PaginatorInterface
    {
        $filters = $context['filters'] ?? [];

        $query = new SearchQuery;
        $query->page = $filters['page'] ?? $query->page;
        $query->randomize = $filters['randomize'] ?? $query->randomize;
        $query->sort = $filters['sort'] ?? $query->sort;
        $query->date_etablissement_min = $filters['date_etablissement_min'] ?? null;
        $query->date_etablissement_max = $filters['date_etablissement_max'] ?? null;
        $query->surface_habitable_min = $filters['surface_habitable_min'] ?? null;
        $query->surface_habitable_max = $filters['surface_habitable_max'] ?? null;
        $query->annee_construction_min = $filters['annee_construction_min'] ?? null;
        $query->annee_construction_max = $filters['annee_construction_max'] ?? null;
        $query->altitude_min = $filters['altitude_min'] ?? null;
        $query->altitude_max = $filters['altitude_max'] ?? null;
        $query->type_batiment = array_map(fn($item) => TypeBatiment::from($item), $filters['type_batiment'] ?? []);
        $query->periode_constrcution = array_map(fn($item) => PeriodeConstruction::from($item), $filters['periode_construction'] ?? []);
        $query->classe_altitude = array_map(fn($item) => ClasseAltitude::from($item), $filters['classe_altitude'] ?? []);
        $query->etiquette_energie = array_map(fn($item) => Etiquette::from($item), $filters['etiquette_energie'] ?? []);
        $query->etiquette_climat = array_map(fn($item) => Etiquette::from($item), $filters['etiquette_climat'] ?? []);
        $query->zone_climatique = array_map(fn($item) => ZoneClimatique::from($item), $filters['zone_climatique'] ?? []);
        $query->code_postal = $filters['code_postal'] ?? [];
        $query->code_departement = $filters['code_departement'] ?? [];
        $query->ban_id = $filters['ban_id'] ?? null;

        $handle = $this->handler;

        return new AuditPaginator(
            collection: $handle($query),
            currentPage: $query->page,
            itemsPerPage: $query->itemsPerPage,
            totalItems: $handle->count(),
        );
    }
}
