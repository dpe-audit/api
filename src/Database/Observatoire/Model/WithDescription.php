<?php

namespace App\Database\Observatoire\Model;

trait WithDescription
{
    public function description(): string
    {
        return $this->description ?? 'Non renseigné';
    }
}
