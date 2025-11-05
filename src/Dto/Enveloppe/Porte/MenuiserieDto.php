<?php

namespace App\Dto\Enveloppe\Porte;

use App\Domain\Enveloppe\Porte\Menuiserie\Menuiserie;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/porte.yaml
 */
final class MenuiserieDto
{
    public function __construct(
        public readonly ?float $largeur_dormant,
        public readonly ?bool $presence_joint,
        public readonly ?bool $presence_retour_isolation,
    ) {}

    public static function from(Menuiserie $data): self
    {
        return new self(
            largeur_dormant: $data->largeur_dormant,
            presence_joint: $data->presence_joint,
            presence_retour_isolation: $data->presence_retour_isolation,
        );
    }

    public function __normalize(): array
    {
        return [
            'largeur_dormant' => $this->largeur_dormant,
            'presence_joint' => $this->presence_joint,
            'presence_retour_isolation' => $this->presence_retour_isolation,
        ];
    }
}
