<?php

namespace App\Dto\Chauffage\Installation;

use App\Domain\Chauffage\Installation\Solaire\Solaire;
use App\Domain\Chauffage\Installation\Solaire\Usage;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/installation.yaml
 */
final class SolaireThermiqueDto
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ?int $annee_installation,
        public readonly ?float $fch,
    ) {}

    public static function from(Solaire $data): self
    {
        return new self(
            usage: $data->usage,
            annee_installation: $data->annee_installation,
            fch: $data->fch,
        );
    }

    public function __normalize(): array
    {
        return [
            'usage' => $this->usage->value,
            'annee_installation' => $this->annee_installation,
            'fch' => $this->fch,
        ];
    }
}
