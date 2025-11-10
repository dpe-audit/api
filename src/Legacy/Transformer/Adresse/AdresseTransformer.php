<?php

namespace App\Legacy\Transformer\Adresse;

use App\Dto\Adresse\AdresseDto;
use App\Legacy\Model\Adresse;

final class AdresseTransformer
{
    private Adresse $adresse;

    public function code_postal(): string
    {
        preg_match('/\d{5}/', $this->adresse->code_postal(), $matches);
        return current($matches);
    }

    public function code_insee(): ?string
    {
        if (null === $this->adresse->code_insee()) {
            return null;
        }
        preg_match('/\d{1}[A-Z0-9]{1}\d{3}/', $this->adresse->code_insee(), $matches);
        return current($matches);
    }

    public function __invoke(Adresse $adresse): AdresseDto
    {
        $this->adresse = $adresse;

        return new AdresseDto(
            nom: $adresse->nom(),
            code_postal: $this->code_postal(),
            code_insee: $this->code_insee() ?? $this->code_postal(),
            commune: $adresse->commune(),
            ban_id: $adresse->ban_id,
        );
    }
}
