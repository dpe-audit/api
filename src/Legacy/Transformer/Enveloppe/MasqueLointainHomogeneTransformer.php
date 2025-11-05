<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{ConfigurationMasque, TypeMasque};
use App\Dto\Enveloppe\Masque\MasqueDto;
use App\Legacy\Model\BaieVitree;

final class MasqueLointainHomogeneTransformer
{
    private BaieVitree $baie_vitree;

    public function configuration(): ?ConfigurationMasque
    {
        return $this->baie_vitree->tv_coef_masque_lointain_homogene_id
            ? ConfigurationMasque::HOMOGENE
            : null;
    }

    public function orientation(): ?Orientation
    {
        return match ($this->baie_vitree->tv_coef_masque_lointain_homogene_id) {
            1, 2, 3, 4 => Orientation::NORD,
            5, 6, 7, 8 => Orientation::SUD,
            9, 10, 11, 12 => Orientation::EST,
            default => null,
        };
    }

    public function hauteur(): ?float
    {
        return match ($this->baie_vitree->tv_coef_masque_lointain_homogene_id) {
            1, 5, 9 => 7.5,
            2, 6, 10 => 22.5,
            3, 7, 11 => 45,
            4, 8, 12 => 75,
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
            id: $baie_vitree->masque_lointain_id(),
            description: 'Masque lointain homodène',
            type: TypeMasque::LOINTAIN,
            configuration: $configuration,
            orientation: $this->orientation(),
            hauteur: $this->hauteur(),
            profondeur: null,
            secteur: null,
        );
    }
}
