<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage, Vitrage};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/baie.yaml
 */
final class VitrageDto
{
    public function __construct(
        public readonly TypeVitrage $type,
        public readonly ?NatureGazLame $nature_lame,
        public readonly ?float $epaisseur_lame,
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
