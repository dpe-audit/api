<?php

namespace App\Dto\Enveloppe\Lnc\Paroi;

use App\Domain\Enveloppe\Lnc\Paroi\Isolation;
use App\Domain\Enveloppe\Lnc\Paroi\Paroi;
use App\Domain\Enveloppe\Lnc\Paroi\ParoiCollection;

final class ParoiDto
{
    public function __construct(
        public string $id,
        public string $description,
        public ?Isolation $isolation,
        public PositionDto $position,
    ) {}

    public static function from(Paroi $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            isolation: $data->isolation(),
            position: PositionDto::from($data->position()),
        );
    }

    /**
     * @return array<self>
     */
    public static function fromCollection(ParoiCollection $data): array
    {
        return $data->map(fn(Paroi $item) => self::from($item))->values();
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'isolation' => $this->isolation?->value,
            'position' => $this->position->__normalize(),
        ];
    }
}
