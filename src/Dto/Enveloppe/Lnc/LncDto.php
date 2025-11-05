<?php

namespace App\Dto\Enveloppe\Lnc;

use App\Domain\Enveloppe\Lnc\{Lnc, TypeLnc};
use App\Dto\Enveloppe\Lnc\Baie\BaieDto;
use App\Dto\Enveloppe\Lnc\Paroi\ParoiDto;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/local_non_chauffe/local_non_chauffe.yaml
 * 
 * @property array<ParoiDto> $parois
 * @property array<BaieDto> $baies
 */
final class LncDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypeLnc $type,
        #[Constraints\All([new Constraints\Type(ParoiDto::class)])]
        #[Constraints\Valid]
        public readonly array $parois,
        #[Constraints\All([new Constraints\Type(BaieDto::class)])]
        #[Constraints\Valid]
        public readonly array $baies,
    ) {}

    public static function from(Lnc $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            type: $data->type(),
            parois: $data->parois()->map(fn($item) => ParoiDto::from($item))->values(),
            baies: $data->baies()->map(fn($item) => BaieDto::from($item))->values(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'parois' => array_map(fn($item) => $item->__normalize(), $this->parois),
            'baies' => array_map(fn($item) => $item->__normalize(), $this->baies),
        ];
    }
}
