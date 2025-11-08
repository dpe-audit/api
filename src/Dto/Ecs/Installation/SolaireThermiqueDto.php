<?php

namespace App\Dto\Ecs\Installation;

use App\Domain\Ecs\Installation\Solaire\{Solaire, Usage};
use App\Validation;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/installation.yaml
 */
final class SolaireThermiqueDto
{
    public function __construct(
        public readonly Usage $usage,
        #[Validation\Annee\AnneeValid]
        public readonly ?int $annee_installation,
        public readonly ?float $fecs,
    ) {}

    public static function from(Solaire $data): self
    {
        return new self(
            usage: $data->usage,
            annee_installation: $data->annee_installation,
            fecs: $data->fecs,
        );
    }

    public function __normalize(): array
    {
        return [
            'usage' => $this->usage->value,
            'annee_installation' => $this->annee_installation,
            'fecs' => $this->fecs,
        ];
    }
}
