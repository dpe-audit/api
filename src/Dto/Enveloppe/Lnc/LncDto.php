<?php

namespace App\Dto\Enveloppe\Lnc;

use App\Domain\Enveloppe\Lnc\{Lnc, LncData, TypeLnc};
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

        public readonly ?LncData $data = null,
    ) {}

    public static function from(Lnc $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            parois: $entity->parois()->map(fn($item) => ParoiDto::from($item))->values(),
            baies: $entity->baies()->map(fn($item) => BaieDto::from($item))->values(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
            'parois' => array_values(array_map(fn($item) => $item->__normalize(), $this->parois)),
            'baies' => array_values(array_map(fn($item) => $item->__normalize(), $this->baies)),
        ];
        if ($this->data) {
            $data['data'] = [
                'uvue' => $this->data->uvue,
                'aue' => $this->data->aue,
                'aiu' => $this->data->aiu,
                'isolation_aue' => $this->data->isolation_aue,
                'isolation_aiu' => $this->data->isolation_aiu,
                'b' => $this->data->b,
                'sse' => $this->data->sse,
            ];
        }
        return $data;
    }
}
