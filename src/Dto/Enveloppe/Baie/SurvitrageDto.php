<?php

namespace App\Dto\Enveloppe\Baie;

use App\Domain\Enveloppe\Baie\Survitrage\{TypeSurvitrage, Survitrage};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/baie.yaml
 */
final class SurvitrageDto
{
    public function __construct(
        public readonly TypeSurvitrage $type,
        public readonly ?float $epaisseur_lame,
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
