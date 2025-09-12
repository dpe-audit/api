<?php

namespace App\Dto\Chauffage;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\TypeChauffage;
use App\Dto\Chauffage\Emetteur\EmetteurDto;
use App\Dto\Chauffage\Generateur\GenerateurDto;
use App\Dto\Chauffage\Installation\InstallationDto;
use App\Dto\Chauffage\Systeme\SystemeDto;
use Symfony\Component\Validator\Constraints;

/**
 * @property array<EmetteurDto> $emetteurs
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 * @property array<SystemeDto> $systemes
 */
final class ChauffageDto
{
    public function __construct(
        #[Constraints\All([new Constraints\Type(EmetteurDto::class)])]
        #[Constraints\Valid]
        public array $emetteurs,

        #[Constraints\All([new Constraints\Type(GenerateurDto::class)])]
        #[Constraints\Valid]
        public array $generateurs,

        #[Constraints\All([new Constraints\Type(InstallationDto::class)])]
        #[Constraints\Valid]
        public array $installations,

        #[Constraints\All([new Constraints\Type(SystemeDto::class)])]
        #[Constraints\Valid]
        public array $systemes,
    ) {}

    public static function from(Chauffage $data): self
    {
        return new self(
            emetteurs: EmetteurDto::fromCollection($data->emetteurs()),
            generateurs: GenerateurDto::fromCollection($data->generateurs()),
            installations: InstallationDto::fromCollection($data->installations()),
            systemes: SystemeDto::fromCollection($data->systemes()),
        );
    }

    #[Constraints\IsTrue]
    public function is_emetteur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($systeme->emetteurs as $id) {
                if (false === array_find($this->emetteurs, fn(EmetteurDto $dto) => $dto->id === $id)) {
                    return false;
                }
            }
        }
        return true;
    }

    #[Constraints\IsTrue]
    public function is_generateur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            if (false === array_find($this->generateurs, fn(GenerateurDto $dto) => $dto->id === $systeme->generateur_id)) {
                return false;
            }
        }
        return true;
    }

    #[Constraints\IsTrue]
    public function is_installation_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            if (false === array_find($this->installations, fn(InstallationDto $dto) => $dto->id === $systeme->installation_id)) {
                return false;
            }
        }
        return true;
    }

    public function is_type_chauffage_valid(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($this->generateurs as $generateur) {
                if ($generateur->id !== $systeme->generateur_id) {
                    continue;
                }
                if ($systeme->type === TypeChauffage::CHAUFFAGE_CENTRAL) {
                    if ($generateur->type && false === $generateur->type->is_chauffage_central()) {
                        return false;
                    }
                }
                if ($systeme->type === TypeChauffage::CHAUFFAGE_DIVISE) {
                    if ($generateur->type && false === $generateur->type->is_chauffage_divise()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function __normalize(): array
    {
        return [
            'emetteurs' => array_map(fn(EmetteurDto $dto) => $dto->__normalize(), $this->emetteurs),
            'generateurs' => array_map(fn(GenerateurDto $dto) => $dto->__normalize(), $this->generateurs),
            'installations' => array_map(fn(InstallationDto $dto) => $dto->__normalize(), $this->installations),
            'systemes' => array_map(fn(SystemeDto $dto) => $dto->__normalize(), $this->systemes),
        ];
    }
}
