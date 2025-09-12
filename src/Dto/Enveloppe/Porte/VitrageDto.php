<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;
use App\Domain\Enveloppe\Porte\Vitrage\Vitrage;

final class VitrageDto
{
    public function __construct(
        public float $surface,
        public ?TypeVitrage $type,
    ) {}

    public static function from(Vitrage $data): self
    {
        return new self(
            surface: $data->surface,
            type: $data->type,
        );
    }

    public function __normalize(): array
    {
        return [
            'surface' => $this->surface,
            'type' => $this->type?->value,
        ];
    }
}
