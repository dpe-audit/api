<?php

namespace App\Legacy\Model;

trait WithDescription
{
    public function description(): string
    {
        return $this->description ? trim($this->description) : 'Non renseigné';
    }
}
