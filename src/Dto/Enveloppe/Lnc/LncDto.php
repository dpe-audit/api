<?php

namespace App\Dto\Enveloppe\Lnc;

use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Lnc\LncCollection;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Dto\Enveloppe\Lnc\Baie\BaieDto;
use App\Dto\Enveloppe\Lnc\Paroi\ParoiDto;

/**
 * @property array<ParoiDto> $parois
 * @property array<BaieDto> $baies
 */
final class LncDto
{
    public function __construct(
        public string $id,
        public string $description,
        public TypeLnc $type,
        public array $parois,
        public array $baies,
    ) {}

    public static function from(Lnc $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            parois: ParoiDto::fromCollection($data->parois()),
            baies: BaieDto::fromCollection($data->baies()),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(LncCollection $data): array
    {
        return $data->map(fn(Lnc $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'parois' => array_map(fn(ParoiDto $item) => $item->__normalize(), $this->parois),
            'baies' => array_map(fn(BaieDto $item) => $item->__normalize(), $this->baies),
        ];
    }
}
