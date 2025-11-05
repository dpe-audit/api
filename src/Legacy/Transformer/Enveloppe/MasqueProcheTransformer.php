<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{ConfigurationMasque, TypeMasque};
use App\Dto\Enveloppe\Masque\MasqueDto;
use App\Legacy\Model\BaieVitree;

final class MasqueProcheTransformer
{
    private BaieVitree $baie_vitree;

    public function configuration(): ?ConfigurationMasque
    {
        return match ($this->baie_vitree->tv_coef_masque_proche_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => ConfigurationMasque::FOND_BALCON,
            13, 14, 15, 16 => ConfigurationMasque::BALCON_OU_AUVENT,
            17 => ConfigurationMasque::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
            18 => ConfigurationMasque::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD,
            default => null,
        };
    }

    public function orientation(): ?Orientation
    {
        return match ($this->baie_vitree->tv_coef_masque_proche_id) {
            1, 2, 3, 4 => Orientation::NORD,
            5, 6, 7, 8 => Orientation::SUD,
            9, 10, 11, 12 => Orientation::EST,
            default => null,
        };
    }

    public function profondeur(): ?float
    {
        return match ($this->baie_vitree->tv_coef_masque_proche_id) {
            1, 5, 9, 13 => 0.5,
            2, 6, 10, 14 => 1.5,
            3, 7, 11, 15 => 2.5,
            4, 8, 12, 16 => 3.5,
            default => null,
        };
    }

    public function __invoke(BaieVitree $baie_vitree): ?MasqueDto
    {
        $this->baie_vitree = $baie_vitree;

        if (null === $configuration = $this->configuration()) {
            return null;
        }

        return new MasqueDto(
            id: $baie_vitree->masque_proche_id(),
            description: 'Masque proche',
            type: TypeMasque::PROCHE,
            configuration: $configuration,
            orientation: $this->orientation(),
            profondeur: $this->profondeur(),
            hauteur: null,
            secteur: null,
        );
    }
}
