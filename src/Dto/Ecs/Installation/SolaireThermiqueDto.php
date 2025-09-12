<?php

namespace App\Dto\Ecs\Installation;

use App\Domain\Ecs\Installation\Solaire\Solaire;
use App\Domain\Ecs\Installation\Solaire\Usage;

final class SolaireThermiqueDto
{
    public function __construct(
        public Usage $usage,
        public ?int $annee_installation,
        public ?float $fecs,
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
