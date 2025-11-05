<?php

namespace App\Legacy\Transformer;

use App\Legacy\Model\Logement;
use App\Legacy\Model\Ressource;

final class Context
{
    public function __construct(
        private Ressource $ressource,
        private ?Logement $logement = null,
    ) {}

    public function set_logement(Logement $logement): void
    {
        $this->logement = $logement;
    }

    public function ressource(): Ressource
    {
        return $this->ressource;
    }

    public function logement(): ?Logement
    {
        return $this->logement;
    }
}
