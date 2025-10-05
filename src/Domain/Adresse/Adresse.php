<?php

namespace App\Domain\Adresse;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/adresse/adresse.yaml
 */
final class Adresse
{
    public function __construct(
        public readonly string $nom,
        public readonly string $code_postal,
        public readonly string $code_insee,
        public readonly string $commune,
        public readonly string $code_departement,
        public readonly ?string $ban_id,
    ) {}

    public static function create(
        string $nom,
        string $code_postal,
        string $code_insee,
        string $commune,
        ?string $ban_id,
    ): self {
        return new self(
            nom: $nom,
            code_postal: $code_postal,
            code_insee: $code_insee,
            commune: $commune,
            ban_id: $ban_id,
            code_departement: \substr($code_insee, 0, 2),
        );
    }
}
