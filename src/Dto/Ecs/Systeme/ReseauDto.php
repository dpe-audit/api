<?php

namespace App\Dto\Ecs\Systeme;

use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Domain\Ecs\Systeme\Reseau\IsolationReseau;
use App\Domain\Ecs\Systeme\Reseau\Reseau;

final class ReseauDto
{
    public function __construct(
        public bool $alimentation_contigue,
        public int $niveaux_desservis,
        public ?IsolationReseau $isolation,
        public ?BouclageReseau $bouclage,
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
