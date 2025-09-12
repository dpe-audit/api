<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Vitrage\NatureGazLame;
use App\Domain\Enveloppe\Baie\Vitrage\TypeVitrage;
use App\Domain\Enveloppe\Baie\Vitrage\Vitrage;

final class VitrageDto
{
    public function __construct(
        public TypeVitrage $type,
        public ?NatureGazLame $nature_lame,
        public ?float $epaisseur_lame,
    ) {}

    public static function from(Vitrage $data): self
    {
        return new self(
            type: $data->type,
            nature_lame: $data->nature_lame,
            epaisseur_lame: $data->epaisseur_lame,
        );
    }

    public function __normalize(): array
    {
        return [
            'type' => $this->type->value,
            'nature_lame' => $this->nature_lame?->value,
            'epaisseur_lame' => $this->epaisseur_lame,
        ];
    }
}
