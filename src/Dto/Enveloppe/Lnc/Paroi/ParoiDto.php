<?php

namespace App\Dto\Enveloppe\Lnc\Paroi;

use App\Domain\Enveloppe\Lnc\Paroi\{Isolation, Paroi};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/local_non_chauffe/paroi.yaml
 */
final class ParoiDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?Isolation $isolation,
        public readonly PositionDto $position,
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
