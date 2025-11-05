<?php

namespace App\Legacy\Model;

interface Ressource
{
    public function administratif(): Administratif;

    public function logement(): Logement;

    public function dpe_immeuble(): ?DpeImmeuble;
}
