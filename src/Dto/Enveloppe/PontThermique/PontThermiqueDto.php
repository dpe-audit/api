<?php

namespace App\Dto\Enveloppe\PontThermique;

use App\Domain\Enveloppe\PontThermique\PontThermique;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/pont_thermique.yaml
 */
final class PontThermiqueDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $longueur,
        public readonly ?float $kpt,
        public readonly LiaisonDto $liaison,
    ) {}

    public static function from(PontThermique $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            longueur: $data->longueur(),
            kpt: $data->kpt(),
            liaison: LiaisonDto::from($data->liaison()),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'longueur' => $this->longueur,
            'kpt' => $this->kpt,
            'liaison' => $this->liaison->__normalize(),
        ];
    }
}
