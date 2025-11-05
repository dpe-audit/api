<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\Vitrage\{TypeVitrage, Vitrage};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/porte.yaml
 */
final class VitrageDto
{
    public function __construct(
        public readonly float $surface,
        public readonly ?TypeVitrage $type,
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
