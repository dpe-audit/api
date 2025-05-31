<?php

namespace App\Api\Audit\Collection;

use App\Api\Audit\Model\{Adresse, AuditData, Batiment};
use App\Domain\Audit\Audit as Entity;

final class Audit
{
    public function __construct(
        public string $id,

        public string $date_etablissement,

        public Adresse $adresse,

        public Batiment $batiment,

        public AuditData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            date_etablissement: $entity->date_etablissement()->format('Y-m-d'),
            adresse: Adresse::from($entity),
            batiment: Batiment::from($entity),
            data: AuditData::from($entity),
        );
    }
}
