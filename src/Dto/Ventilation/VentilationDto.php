<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Ventilation;
use Symfony\Component\Validator\Constraints;

/**
 * @property array<GenerateurDto> $generateurs
 * @property array<InstallationDto> $installations
 */
final class VentilationDto
{
    public function __construct(
        #[Constraints\All([new Constraints\Type(GenerateurDto::class)])]
        #[Constraints\Valid]
        public array $generateurs,

        #[Constraints\All([new Constraints\Type(InstallationDto::class)])]
        #[Constraints\Valid]
        public array $installations,
    ) {}

    public static function from(Ventilation $data): self
    {
        return new self(
            generateurs: GenerateurDto::fromCollection($data->generateurs()),
            installations: InstallationDto::fromCollection($data->installations()),
        );
    }

    #[Constraints\IsTrue]
    public function is_generateur_exists(): bool
    {
        foreach ($this->installations as $installation) {
            if ($installation->generateur_id === null) {
                continue;
            }
            foreach ($this->generateurs as $generateur) {
                if ($generateur->id === $installation->generateur_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    public function __normalize(): array
    {
        return [
            'generateurs' => array_map(fn(GenerateurDto $dto) => $dto->__normalize(), $this->generateurs),
            'installations' => array_map(fn(InstallationDto $dto) => $dto->__normalize(), $this->installations),
        ];
    }
}
