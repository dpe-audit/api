<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;
use App\Domain\Enveloppe\Masque\SecteurMasque;

final class XMLMasqueLointainNonHomogene
{
    public function __construct(
        public readonly int $tv_coef_masque_lointain_non_homogene_id,
    ) {}

    /**
     * XSD logement/enveloppe/baie_vitree_collection/baie_vitree/masque_lointain_non_homogene_collection/masque_lointain_non_homogene
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            tv_coef_masque_lointain_non_homogene_id: (int) $xml->donnee_entree->tv_coef_masque_lointain_non_homogene_id,
        );
    }

    /**
     * XSD logement/enveloppe/baie_vitree_collection/baie_vitree/masque_lointain_non_homogene_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->masque_lointain_non_homogene as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function configuration(): ConfigurationMasque
    {
        return ConfigurationMasque::NON_HOMOGENE;
    }

    public function orientation(): Orientation
    {
        return match ($this->tv_coef_masque_lointain_non_homogene_id) {
            1, 2, 3, 4, 5, 6, 7, 8 => Orientation::NORD,
            9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20 => Orientation::EST,
        };
    }

    public function hauteur(): float
    {
        return match ($this->tv_coef_masque_lointain_non_homogene_id) {
            1, 5, 9, 13, 17 => 7.5,
            2, 6, 10, 14, 18 => 22.5,
            3, 7, 11, 15, 19 => 45,
            4, 8, 12, 16, 20 => 75,
        };
    }

    public function secteur(): SecteurMasque
    {
        return match ($this->tv_coef_masque_lointain_non_homogene_id) {
            1, 2, 3, 4 => SecteurMasque::LATERAL,
            5, 6, 7, 8 => SecteurMasque::CENTRAL,
            9, 10, 11, 12 => SecteurMasque::LATERAL_SUD,
            13, 14, 15, 16 => SecteurMasque::CENTRAL_SUD,
            17, 18, 19, 20 => SecteurMasque::LATERAL,
        };
    }
}
