<?php

namespace App\Dto\Enveloppe\PontThermique;

use App\Domain\Enveloppe\PontThermique\PontThermique;
use App\Domain\Enveloppe\PontThermique\PontThermiqueCollection;

final class PontThermiqueDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $longueur,
        public ?float $kpt,
        public LiaisonDto $liaison,
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

    /**
     * @return array<self>
     */
    public static function fromCollection(PontThermiqueCollection $data): array
    {
        return $data->map(fn(PontThermique $item) => self::from($item))->values();
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
