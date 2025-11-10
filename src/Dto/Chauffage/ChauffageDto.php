<?php

namespace App\Dto\Chauffage;

use App\Domain\Chauffage\{Chauffage, ChauffageData};
use App\Dto\Chauffage\Emetteur\EmetteurDto;
use App\Dto\Chauffage\Generateur\GenerateurDto;
use App\Dto\Chauffage\Installation\InstallationDto;
use App\Dto\Chauffage\Systeme\SystemeDto;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/chauffage.yaml
 * 
 * @property array<EmetteurDto> $emetteurs
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 * @property array<SystemeDto> $systemes
 */
#[Validation\Chauffage\ChauffageValid]
final class ChauffageDto
{
    public function __construct(
        #[Constraints\All([new Constraints\Type(EmetteurDto::class)])]
        #[Constraints\Valid]
        public readonly array $emetteurs,
        #[Constraints\All([new Constraints\Type(GenerateurDto::class)])]
        #[Constraints\Valid]
        public readonly array $generateurs,
        #[Constraints\All([new Constraints\Type(InstallationDto::class)])]
        #[Constraints\Valid]
        public readonly array $installations,
        #[Constraints\All([new Constraints\Type(SystemeDto::class)])]
        #[Constraints\Valid]
        public readonly array $systemes,

        public readonly ?ChauffageData $data = null,
    ) {}

    public static function from(Chauffage $entity): self
    {
        return new self(
            emetteurs: $entity->emetteurs()->map(fn($item) => EmetteurDto::from($item))->values(),
            generateurs: $entity->generateurs()->map(fn($item) => GenerateurDto::from($item))->values(),
            installations: $entity->installations()->map(fn($item) => InstallationDto::from($item))->values(),
            systemes: $entity->systemes()->map(fn($item) => SystemeDto::from($item))->values(),
            data: $entity->data(),
        );
    }

    public function find_emetteur(string $id): ?EmetteurDto
    {
        return array_find($this->emetteurs, fn($item) => $item->id === $id);
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
            'emetteurs' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->emetteurs)),
            'generateurs' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->generateurs)),
            'installations' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->installations)),
            'systemes' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->systemes)),
        ];
        if ($this->data) {
            $data['data'] = [
                'bch' => $this->data->bch,
                'pertes_generation' => $this->data->pertes_generation,
                'pertes_generation_recuperables' => $this->data->pertes_generation_recuperables,
                'consommations' => $this->data->consommations?->__normalize(),
            ];
        }
        return $data;
    }
}
