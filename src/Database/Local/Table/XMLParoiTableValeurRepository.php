<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Enveloppe\Paroi\Mitoyennete;

abstract class XMLParoiTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function b(Mitoyennete $mitoyennete): ?float
    {
        return $this->db->repository('paroi.b')
            ->createQuery()
            ->and('mitoyennete', $mitoyennete)
            ->getOne()
            ?->floatval('b');
    }
}
