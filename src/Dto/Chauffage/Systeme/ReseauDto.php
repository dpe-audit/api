<?php

namespace App\Dto\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, Reseau, TypeDistribution};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/systeme.yaml
 */
final class ReseauDto
{
    public function __construct(
        public readonly TypeDistribution $type_distribution,
        public readonly bool $presence_circulateur_externe,
        public readonly int $niveaux_desservis,
        public readonly ?IsolationReseau $isolation,
    ) {}

    public static function from(Reseau $data): self
    {
        return new self(
            type_distribution: $data->type_distribution,
            presence_circulateur_externe: $data->presence_circulateur_externe,
            niveaux_desservis: $data->niveaux_desservis,
            isolation: $data->isolation,
        );
    }

    public function __normalize(): array
    {
        return [
            'type_distribution' => $this->type_distribution->value,
            'presence_circulateur_externe' => $this->presence_circulateur_externe,
            'niveaux_desservis' => $this->niveaux_desservis,
            'isolation' => $this->isolation?->value,
        ];
    }
}
