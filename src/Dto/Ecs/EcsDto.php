<?php

namespace App\Dto\Ecs;

use App\Domain\Ecs\Ecs;
use App\Dto\Ecs\Generateur\GenerateurDto;
use App\Dto\Ecs\Installation\InstallationDto;
use App\Dto\Ecs\Systeme\SystemeDto;
use Symfony\Component\Validator\Constraints;
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/ecs.yaml
 * 
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 * @property array<SystemeDto> $systemes
 */
#[Validation\Ecs\EcsValid]
final class EcsDto
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

    public static function from(Ecs $data): self
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
