<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Refroidissement;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/refroidissement.yaml
 * 
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 * @property array<SystemeDto> $systemes
 */
#[Validation\Refroidissement\RefroidissementValid]
final class RefroidissementDto
{
    public function __construct(
        #[Constraints\All([new Constraints\Type(GenerateurDto::class)])]
        #[Constraints\Valid]
        public readonly array $generateurs,

        #[Constraints\All([new Constraints\Type(InstallationDto::class)])]
        #[Constraints\Valid]
        public readonly array $installations,

        #[Constraints\All([new Constraints\Type(SystemeDto::class)])]
        #[Constraints\Valid]
        public readonly array $systemes,
    ) {}

    public static function from(Refroidissement $data): self
    {
        return new self(
            generateurs: $data->generateurs()->map(fn($item) => GenerateurDto::from($item))->values(),
            installations: $data->installations()->map(fn($item) => InstallationDto::from($item))->values(),
            systemes: $data->systemes()->map(fn($item) => SystemeDto::from($item))->values(),
        );
    }

    public function find_generateur(string $id): ?GenerateurDto
    {
        return array_find($this->generateurs, fn($item) => $item->id === $id);
    }

    public function find_installation(string $id): ?InstallationDto
    {
        return array_find($this->installations, fn($item) => $item->id === $id);
    }

    public function __normalize(): array
    {
        return [
            'generateurs' => array_map(fn($dto) => $dto->__normalize(), $this->generateurs),
            'installations' => array_map(fn($dto) => $dto->__normalize(), $this->installations),
            'systemes' => array_map(fn($dto) => $dto->__normalize(), $this->systemes),
        ];
    }
}
