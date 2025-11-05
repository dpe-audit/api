<?php

namespace App\Dto\Adresse;

use App\Domain\Adresse\Adresse;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/adresse/adresse.yaml
 */
final class AdresseDto
{
    public function __construct(
        public readonly string $nom,
        public readonly string $code_postal,
        public readonly string $code_insee,
        public readonly string $commune,
        public readonly ?string $ban_id,
    ) {}

    public static function from(Adresse $data): self
    {
        return new self(
            nom: $data->nom,
            code_postal: $data->code_postal,
            code_insee: $data->code_insee,
            commune: $data->commune,
            ban_id: $data->ban_id,
        );
    }

    public function to(): Adresse
    {
        return Adresse::create(
            nom: $this->nom,
            code_postal: $this->code_postal,
            code_insee: $this->code_insee,
            commune: $this->commune,
            ban_id: $this->ban_id,
        );
    }

    public function __normalize(): array
    {
        return [
            'nom' => $this->nom,
            'code_postal' => $this->code_postal,
            'code_insee' => $this->code_insee,
            'commune' => $this->commune,
            'ban_id' => $this->ban_id,
        ];
    }
}
