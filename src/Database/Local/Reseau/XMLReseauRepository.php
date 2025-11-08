<?php

namespace App\Database\Local\Reseau;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Reseau\{Reseau, ReseauRepository};

final class XMLReseauRepository implements ReseauRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function find(string $id): ?Reseau
    {
        $record = $this->db->repository('reseau')
            ->createQuery()
            ->and('id', $id)
            ->getOne();

        return $record ? new Reseau(
            id: $id,
            contenu_co2: $record->floatval('contenu_co2'),
            contenu_co2_acv: $record->floatval('contenu_co2_acv'),
            taux_enr: $record->floatval('taux_enr'),
        ) : null;
    }
}
