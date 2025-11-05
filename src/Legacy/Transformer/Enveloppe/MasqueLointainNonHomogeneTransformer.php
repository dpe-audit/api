<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{ConfigurationMasque, SecteurMasque, TypeMasque};
use App\Dto\Enveloppe\Masque\MasqueDto;
use App\Legacy\Model\MasqueLointainNonHomogene;

final class MasqueLointainNonHomogeneTransformer
{
    private MasqueLointainNonHomogene $masque_lointain_non_homogene;

    public function configuration(): ConfigurationMasque
    {
        return ConfigurationMasque::NON_HOMOGENE;
    }

    public function orientation(): Orientation
    {
        return match ($this->masque_lointain_non_homogene->tv_coef_masque_lointain_non_homogene_id) {
            1, 2, 3, 4, 5, 6, 7, 8 => Orientation::NORD,
            9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20 => Orientation::EST,
        };
    }

    public function hauteur(): float
    {
        return match ($this->masque_lointain_non_homogene->tv_coef_masque_lointain_non_homogene_id) {
            1, 5, 9, 13, 17 => 7.5,
            2, 6, 10, 14, 18 => 22.5,
            3, 7, 11, 15, 19 => 45,
            4, 8, 12, 16, 20 => 75,
        };
    }

    public function secteur(): SecteurMasque
    {
        return match ($this->masque_lointain_non_homogene->tv_coef_masque_lointain_non_homogene_id) {
            1, 2, 3, 4 => SecteurMasque::LATERAL,
            5, 6, 7, 8 => SecteurMasque::CENTRAL,
            9, 10, 11, 12 => SecteurMasque::LATERAL_SUD,
            13, 14, 15, 16 => SecteurMasque::CENTRAL_SUD,
            17, 18, 19, 20 => SecteurMasque::LATERAL,
        };
    }

    public function __invoke(MasqueLointainNonHomogene $masque_lointain_non_homogene): MasqueDto
    {
        $this->masque_lointain_non_homogene = $masque_lointain_non_homogene;

        return new MasqueDto(
            id: $masque_lointain_non_homogene->id(),
            description: 'Masque lointain non homogène',
            type: TypeMasque::LOINTAIN,
            configuration: $this->configuration(),
            orientation: $this->orientation(),
            hauteur: $this->hauteur(),
            secteur: $this->secteur(),
            profondeur: null,
        );
    }
}
