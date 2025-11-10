<?php

namespace App\Dto\Ecs;

use App\Domain\Ecs\{Ecs, EcsData};
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

        public readonly ?EcsData $data = null,
    ) {}

    public static function from(Ecs $entity): self
    {
        return new self(
            generateurs: $entity->generateurs()->map(fn($item) => GenerateurDto::from($item))->values(),
            installations: $entity->installations()->map(fn($item) => InstallationDto::from($item))->values(),
            systemes: $entity->systemes()->map(fn($item) => SystemeDto::from($item))->values(),
            data: $entity->data(),
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
        $data = [
            'generateurs' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->generateurs)),
            'installations' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->installations)),
            'systemes' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->systemes)),
        ];
        if ($this->data) {
            $data['data'] = [
                'nmax' => $this->data->nmax,
                'nadeq' => $this->data->nadeq,
                'becs' => $this->data->becs,
                'cef_ecs' => $this->data->cef_ecs,
                'cep_ecs' => $this->data->cep_ecs,
                'eges_ecs' => $this->data->eges_ecs,
                'cef_aux' => $this->data->cef_aux,
                'cep_aux' => $this->data->cep_aux,
                'eges_aux' => $this->data->eges_aux,
                'pertes_generation' => $this->data->pertes_generation,
                'pertes_generation_recuperables' => $this->data->pertes_generation_recuperables,
                'pertes_stockage' => $this->data->pertes_stockage,
                'pertes_stockage_recuperables' => $this->data->pertes_stockage_recuperables,
                'pertes_distribution' => $this->data->pertes_distribution,
                'pertes_distribution_recuperables' => $this->data->pertes_distribution_recuperables,
            ];
        }
        return $data;
    }
}
