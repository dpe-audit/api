<?php

namespace App\Dto\Enveloppe\PontThermique;

use App\Domain\Enveloppe\PontThermique\{PontThermique, PontThermiqueData};

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
        public readonly ?PontThermiqueData $data = null,
    ) {}

    public static function from(PontThermique $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            longueur: $entity->longueur(),
            kpt: $entity->kpt(),
            liaison: LiaisonDto::from($entity->liaison()),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'longueur' => $this->longueur,
            'kpt' => $this->kpt,
            'liaison' => $this->liaison->__normalize(),
        ];
        if ($this->data) {
            $data['data'] = [
                'k' => $this->data->k,
                'pt' => $this->data->pt,
            ];
        }
        return $data;
    }
}
