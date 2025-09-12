<?php

namespace App\Dto\Enveloppe\DoubleFenetre;

use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\Survitrage;

final class SurvitrageDto
{
    public function __construct(
        public TypeSurvitrage $type,
        public ?float $epaisseur_lame,
    ) {}

    public static function from(Survitrage $data): self
    {
        return new self(
            type: $data->type,
            epaisseur_lame: $data->epaisseur_lame,
        );
    }

    public function __normalize(): array
    {
        return [
            'type' => $this->type->value,
            'epaisseur_lame' => $this->epaisseur_lame,
        ];
    }
}
