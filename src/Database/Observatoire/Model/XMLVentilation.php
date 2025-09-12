<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Exposition;
use App\Domain\Ventilation\Generateur\TypeGenerateur;
use App\Domain\Ventilation\Generateur\TypeVmc;
use App\Domain\Ventilation\Installation\TypeVentilation;

final class XMLVentilation extends XMLUniqueElement
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_ventile,
        public readonly bool $plusieurs_facade_exposee,
        public readonly ?int $tv_q4pa_conv_id,
        public readonly ?float $q4pa_conv_saisi,
        public readonly int $enum_methode_saisie_q4pa_conv_id,
        public readonly int $tv_debits_ventilation_id,
        public readonly int $enum_type_ventilation_id,
        public readonly bool $ventilation_post_2012,
        public readonly ?string $ref_produit_ventilation,
        public readonly ?float $cle_repartition_ventilation,
        public readonly ?float $pvent_moy,
        public readonly float $q4pa_conv,
        public readonly float $conso_auxiliaire_ventilation,
        public readonly float $hperm,
        public readonly float $hvent
    ) {}

    /**
     * XSD logement/ventilation_collection/ventilation
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->reference,
            description: (string) $xml->description ?: null,
            surface_ventile: (float) $xml->surface_ventile,
            plusieurs_facade_exposee: (bool)(int) $xml->plusieurs_facade_exposee,
            tv_q4pa_conv_id: (int) $xml->tv_q4pa_conv_id ?: null,
            q4pa_conv_saisi: (float) $xml->q4pa_conv_saisi ?: null,
            enum_methode_saisie_q4pa_conv_id: (int) $xml->enum_methode_saisie_q4pa_conv_id,
            tv_debits_ventilation_id: (int) $xml->tv_debits_ventilation_id,
            enum_type_ventilation_id: (int) $xml->enum_type_ventilation_id,
            ventilation_post_2012: (bool)(int) $xml->ventilation_post_2012,
            ref_produit_ventilation: (string) $xml->ref_produit_ventilation ?: null,
            cle_repartition_ventilation: (float) $xml->cle_repartition_ventilation ?: null,
            pvent_moy: (float) $xml->pvent_moy ?: null,
            q4pa_conv: (float) $xml->q4pa_conv,
            conso_auxiliaire_ventilation: (float) $xml->conso_auxiliaire_ventilation,
            hperm: (float) $xml->hperm,
            hvent: (float) $xml->hvent
        );
    }

    /**
     * XSD logement/ventilation_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->ventilation as $item) {
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

    public function exposition(): Exposition
    {
        return match ($this->plusieurs_facade_exposee) {
            true => Exposition::EXPOSITION_MULTIPLE,
            false => Exposition::EXPOSITION_SIMPLE,
        };
    }

    public function type_ventilation(): TypeVentilation
    {
        return match ($this->enum_type_ventilation_id) {
            1 => TypeVentilation::VENTILATION_NATURELLE_OUVERTURE_FENETRES,
            2 => TypeVentilation::VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES,
            25 => TypeVentilation::VENTILATION_NATURELLE_CONDUIT,
            34 => TypeVentilation::VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES,
            default => TypeVentilation::VENTILATION_MECANIQUE,
        };
    }

    public function type_generateur(): ?TypeGenerateur
    {
        return match ($this->enum_type_ventilation_id) {
            3, 4, 5, 6, 7, 8, 9, 13, 14, 15 => TypeGenerateur::VMC_SIMPLE_FLUX,
            10, 11, 12 => TypeGenerateur::VMC_SIMPLE_FLUX_GAZ,
            16, 17, 18 => TypeGenerateur::VMC_BASSE_PRESSION,
            19, 20, 21, 22, 23, 24 => TypeGenerateur::VMC_DOUBLE_FLUX,
            26, 27, 28, 29, 30, 31 => TypeGenerateur::VENTILATION_HYBRIDE,
            32, 33 => TypeGenerateur::VENTILATION_MECANIQUE,
            35, 36, 37, 38 => TypeGenerateur::PUIT_CLIMATIQUE,
            default => null,
        };
    }

    public function type_vmc(): ?TypeVmc
    {
        if (null === $this->type_generateur()) {
            return null;
        }
        if (false === $this->type_generateur()->is_vmc()) {
            return null;
        }
        return match ($this->enum_type_ventilation_id) {
            3, 4, 5, 6, 16, 26, 27, 28 => TypeVmc::AUTOREGLABLE,
            7, 8, 9, 17 => TypeVmc::HYGROREGLABLE_TYPE_A,
            13, 14, 15, 18, 29, 30, 31 => TypeVmc::HYGROREGLABLE_TYPE_B,
            default => match ($this->pvent_moy) {
                35, 65 => TypeVmc::AUTOREGLABLE,
                15, 50 => TypeVmc::HYGROREGLABLE_TYPE_A,
                80, 35 => TypeVmc::HYGROREGLABLE_TYPE_B,
                default => TypeVmc::AUTOREGLABLE,
            }
        };
    }

    public function annee_installation(XMLRessource $xml): ?int
    {
        return match ($this->enum_type_ventilation_id) {
            3 => 1981,
            4, 7, 10, 13, 26, 29 => 2000,
            5, 8, 11, 14, 19, 21, 23, 27, 30, 32, 35, 37 => 2012,
            6, 9, 12, 15, 20, 22, 24, 28, 31, 33, 36, 38 => $xml->administratif->annee_etablissement(),
            default => null,
        };
    }

    public function presence_echangeur_thermique(): bool
    {
        return match ($this->enum_type_ventilation_id) {
            19, 20, 21, 22, 37, 38 => true,
            23, 24, 35, 36 => false,
            default => false,
        };
    }

    public function generateur_collectif(): bool
    {
        return match ($this->enum_type_ventilation_id) {
            21, 22 => true,
            default => false,
        };
    }
}
