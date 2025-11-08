<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Ventilation;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ventilation/ventilation.yaml
 * 
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 */
#[Validation\Ventilation\VentilationValid]
final class VentilationDto
{
    public function __construct(
        #[Constraints\All([new Constraints\Type(GenerateurDto::class)])]
        #[Constraints\Valid]
        public readonly array $generateurs,

        #[Constraints\All([new Constraints\Type(InstallationDto::class)])]
        #[Constraints\Valid]
        public readonly array $installations,
    ) {}

    public static function from(Ventilation $data): self
    {
        return new self(
            generateurs: $data->generateurs()->map(fn($item) => GenerateurDto::from($item))->values(),
            installations: $data->installations()->map(fn($item) => InstallationDto::from($item))->values(),
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
        ];
    }
}
