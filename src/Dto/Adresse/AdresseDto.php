<?php

namespace App\Dto\Adresse;

use App\Domain\Adresse\Adresse;

final class AdresseDto
{
    public function __construct(
        public string $nom,
        public string $code_postal,
        public string $code_insee,
        public string $commune,
        public ?string $ban_id,
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
