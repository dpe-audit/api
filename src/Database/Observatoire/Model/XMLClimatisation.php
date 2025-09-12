<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Refroidissement\Generateur\EnergieGenerateur;
use App\Domain\Refroidissement\Generateur\TypeGenerateur;

final class XMLClimatisation extends XMLUniqueElement
{
    public function __construct(
        public readonly ?string $reference,
        public readonly ?string $description,
        public readonly ?float $surface_clim,
        public readonly ?int $tv_seer_id,
        public readonly ?int $nombre_logement_echantillon,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly int $enum_type_generateur_fr_id,
        public readonly ?int $enum_type_energie_id,
        public readonly int $enum_periode_installation_fr_id,
        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly ?float $cle_repartition_clim,
        public readonly ?string $ref_produit_fr,
        public readonly float $eer,
        public readonly float $besoin_fr,
        public readonly float $conso_fr,
        public readonly float $conso_fr_depensier
    ) {}

    /**
     * XSD logement/climatisation_collection/climatisation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            surface_clim: (float) $xml->donnee_entree->surface_clim ?: null,
            tv_seer_id: (int) $xml->donnee_entree->tv_seer_id ?: null,
            nombre_logement_echantillon: (int) $xml->donnee_entree->nombre_logement_echantillon ?: null,
            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            enum_type_generateur_fr_id: (int) $xml->donnee_entree->enum_type_generateur_fr_id,
            enum_type_energie_id: (int) $xml->donnee_entree->enum_type_energie_id ?: null,
            enum_periode_installation_fr_id: (int) $xml->donnee_entree->enum_periode_installation_fr_id,
            enum_methode_saisie_carac_sys_id: (int) $xml->donnee_entree->enum_methode_saisie_carac_sys_id,
            cle_repartition_clim: (float) $xml->donnee_entree->cle_repartition_clim ?: null,
            ref_produit_fr: (string) $xml->donnee_entree->ref_produit_fr ?: null,
            eer: (float) $xml->donnee_intermediaire->eer,
            besoin_fr: (float) $xml->donnee_intermediaire->besoin_fr,
            conso_fr: (float) $xml->donnee_intermediaire->conso_fr,
            conso_fr_depensier: (float) $xml->donnee_intermediaire->conso_fr_depensier
        );
    }

    /**
     * XSD logement/climatisation_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->climatisation as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function identifiers(): array
    {
        return [$this->reference];
    }

    public function description(): string
    {
        return $this->description ?? 'Description non renseignée';
    }

    public function type_generateur(): TypeGenerateur
    {
        return match ($this->enum_type_generateur_fr_id) {
            1, 2, 3 => TypeGenerateur::PAC_AIR_AIR,
            4, 5, 6, 7 => TypeGenerateur::PAC_AIR_EAU,
            8, 9, 10, 11 => TypeGenerateur::PAC_EAU_EAU,
            12, 13, 14, 15 => TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => TypeGenerateur::PAC_GEOTHERMIQUE,
            20 => TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE,
            21 => TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE,
            22 => TypeGenerateur::AUTRE,
            23 => TypeGenerateur::RESEAU_FROID,
        };
    }

    public function energie_generateur(): EnergieGenerateur
    {
        if ($this->enum_type_energie_id) {
            return match ($this->enum_type_energie_id) {
                1, 12 => EnergieGenerateur::ELECTRICITE,
                2 => EnergieGenerateur::GAZ_NATUREL,
                9, 10, 13 => EnergieGenerateur::GPL,
                15 => EnergieGenerateur::RESEAU_FROID,
            };
        }
        return match ($this->enum_type_generateur_fr_id) {
            21 => EnergieGenerateur::GAZ_NATUREL,
            23 => EnergieGenerateur::RESEAU_FROID,
            default => EnergieGenerateur::ELECTRICITE,
        };
    }

    public function annee_installation(XMLRessource $xml): int
    {
        return match ($this->enum_type_generateur_fr_id) {
            1, 4, 8, 12, 16 => 2007,
            2, 5, 9, 13, 17 => 2014,
            6, 10, 14, 18 => 2016,
            3, 7, 11, 15, 19 => $xml->administratif->annee_etablissement(),
            20, 21, 22, 23 => match ($this->enum_periode_installation_fr_id) {
                1 => 2007,
                2 => 2014,
                3 => $xml->administratif->annee_etablissement(),
            }
        };
    }

    public function seer(): ?float
    {
        return $this->eer ? $this->eer / 0.95 : null;
    }
}
