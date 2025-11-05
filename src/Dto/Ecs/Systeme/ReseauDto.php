<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Reseau\{BouclageReseau, IsolationReseau, Reseau};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/systeme.yaml
 */
final class ReseauDto
{
    public function __construct(
        public readonly bool $alimentation_contigue,
        public readonly int $niveaux_desservis,
        public readonly ?IsolationReseau $isolation,
        public readonly ?BouclageReseau $bouclage,
    ) {}

    public static function from(Reseau $data): self
    {
        return new self(
            alimentation_contigue: $data->alimentation_contigue,
            niveaux_desservis: $data->niveaux_desservis,
            isolation: $data->isolation,
            bouclage: $data->bouclage,
        );
    }

    public function __normalize(): array
    {
        return [
            'alimentation_contigue' => $this->alimentation_contigue,
            'niveaux_desservis' => $this->niveaux_desservis,
            'isolation' => $this->isolation?->value,
            'bouclage' => $this->bouclage?->value,
        ];
    }
}
