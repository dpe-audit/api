<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLElement;
use App\Domain\Audit\{Audit, AuditCollection, AuditRepository};
use App\Domain\Audit\Enum\{ClasseAltitude, Etiquette, PeriodeConstruction, TypeBatiment};
use App\Domain\Common\Enum\{CodeRegion, ZoneClimatique};
use App\Domain\Common\ValueObject\Id;
use App\Serializer\Opendata\{JSONAuditDeserializer, XMLAuditDeserializer};
use App\Services\Observatoire\Observatoire;
use App\Services\Opendata\Opendata;

final class OpendataAuditRepository implements AuditRepository
{
    private ?int $count = null;
    private array $query = [];

    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly Opendata $opendata,
        private readonly XMLAuditDeserializer $xml_deserializer,
        private readonly JSONAuditDeserializer $json_denormalizer,
    ) {}

    public function find(Id $id): ?Audit
    {
        if ($content = $this->observatoire->audits($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->xml_deserializer->deserialize($xml);
        }
        if ($content = $this->observatoire->dpe($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->xml_deserializer->deserialize($xml);
        }
        return null;
    }

    public function search(int $page = 1, bool $randomize = false): AuditCollection
    {
        $this->count = null;
        $this->query['page'] = $page;
        $this->query['size'] = 100;
        $this->query['qs'] = '_exists_:classe_altitude AND _exists_:code_insee_ban AND _exists_:periode_construction';
        $this->query['version_dpe_in'] = ['2.2', '2.3', '2.4'];
        $this->query['code_region_ban_in'] = array_diff(
            array_column(CodeRegion::cases(), 'value'),
            array_column(CodeRegion::cases_outre_mer(), 'value'),
        );

        $response = $this->opendata->lines($this->query);

        if ($response && $randomize) {
            $response = $this->opendata->lines(array_merge($this->query, ['page' => random_int(1, 100)]));
        }
        if (null === $response) {
            throw new \RuntimeException($this->opendata->getError());
        };

        $this->query = [];
        $this->count = $response->total;

        return new AuditCollection(
            array_map(
                fn(array $item) => $this->json_denormalizer->denormalize($item),
                $response->results,
            ),
        );
    }

    public function count(): int
    {
        return $this->count;
    }

    public function sort(string $name): static
    {
        $this->query['sort'] = $name;
        return $this;
    }

    public function with_date_etablissement(?\DateTimeInterface $min = null, ?\DateTimeInterface $max = null): static
    {
        $min ? $this->query['date_etablieement_dpe_gte'] = $min->format('Y-m-d') : null;
        $max ? $this->query['date_etablieement_dpe_lte'] = $max->format('Y-m-d') : null;
        return $this;
    }

    public function with_surface_habitable(?float $min = null, ?float $max = null): static
    {
        $min ? $this->query['surface_habitable_logement_gte'] = $min : null;
        $max ? $this->query['surface_habitable_logement_lte'] = $max : null;
        return $this;
    }

    public function with_annee_construction(?int $min = null, ?int $max = null): static
    {
        $min ? $this->query['annee_construction_gte'] = $min : null;
        $max ? $this->query['annee_construction_lte'] = $max : null;
        return $this;
    }

    public function with_altitude(?float $min = null, ?float $max = null): static
    {
        $min ? $this->query['altitude_gte'] = $min : null;
        $max ? $this->query['altitude_lte'] = $max : null;
        return $this;
    }

    public function with_type_batiment(TypeBatiment ...$filters): static
    {
        $filters ? $this->query['type_batiment_in'] = array_column($filters, 'value') : null;
        return $this;
    }

    public function with_periode_construction(PeriodeConstruction ...$filters): static
    {
        foreach ($filters as $filter) {
            $this->query['annee_construction_gte'] = min(array_filter([
                $this->query['annee_construction_gte'] ?? null,
                $filter->min(),
            ]));
            $this->query['annee_construction_lte'] = max(array_filter([
                $this->query['annee_construction_lte'] ?? null,
                $filter->max(),
            ]));
        }
        return $this;
    }

    public function with_classe_altitude(ClasseAltitude ...$filters): static
    {
        foreach ($filters as $filter) {
            $this->query['altitude_gte'] = min(array_filter([
                $this->query['altitude_gte'] ?? null,
                $filter->min(),
            ]));
            $this->query['altitude_lte'] = max(array_filter([
                $this->query['altitude_lte'] ?? null,
                $filter->max(),
            ]));
        }
        return $this;
    }

    public function with_etiquette_energie(Etiquette ...$filters): static
    {
        $filters ? $this->query['etiquette_dpe_in'] = array_column($filters, 'value') : null;
        return $this;
    }

    public function with_etiquette_climat(Etiquette ...$filters): static
    {
        $filters ? $this->query['etiquette_ges_in'] = array_column($filters, 'value') : null;
        return $this;
    }

    public function with_code_postal(string ...$filters): static
    {
        $filters ? $this->query['ban_code_postal_in'] = array_column($filters, 'value') : null;
        return $this;
    }

    public function with_code_departement(string ...$filters): static
    {
        $filters ? $this->query['ban_code_departement_in'] = array_column($filters, 'value') : null;
        return $this;
    }

    public function with_adresse(?string $ban_id = null): static
    {
        $ban_id ? $this->query['ban_id'] = $ban_id : null;
        return $this;
    }

    public function with_zone_climatique(ZoneClimatique ...$filters): static
    {
        $filters ? $this->query['zone_climatique_in'] = array_column($filters, 'value') : null;
        return $this;
    }
}
