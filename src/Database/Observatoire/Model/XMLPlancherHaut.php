<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\PlancherHaut\Configuration;
use App\Domain\Enveloppe\PlancherHaut\Inertie;
use App\Domain\Enveloppe\PlancherHaut\Isolation\EtatIsolation;
use App\Domain\Enveloppe\PlancherHaut\Isolation\TypeIsolation;
use App\Domain\Enveloppe\PlancherHaut\Position\Mitoyennete;
use App\Domain\Enveloppe\PlancherHaut\TypePlancherHaut;

final class XMLPlancherHaut extends XMLParoiOpaque
{
    public function __construct(
        public readonly ?float $uph0_saisi,
        public readonly ?float $uph_saisi,
        public readonly ?int $enum_type_plancher_haut_id,
        public readonly ?int $tv_uph0_id,
        public readonly int $tv_uph_id,

        public readonly float $uph,
        public readonly float $uph0
    ) {}

    /**
     * XSD logement/enveloppe/plancher_haut_collection/plancher_haut
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            uph0_saisi: (float) $xml->donnee_entree->uph0_saisi ?: null,
            uph_saisi: (float) $xml->donnee_entree->uph_saisi ?: null,
            enum_type_plancher_haut_id: (int) $xml->donnee_entree->enum_type_plancher_haut_id,
            tv_uph0_id: (int) $xml->donnee_entree->tv_uph0_id ?: null,
            tv_uph_id: (int) $xml->donnee_entree->tv_uph_id,
            uph: (float) $xml->donnee_intermediaire->uph,
            uph0: (float) $xml->donnee_intermediaire->uph0 ?: null
        ))->set($xml);
    }

    /**
     * XSD logement/enveloppe/plancher_haut_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->plancher_haut as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function configuration(): Configuration
    {
        return match($this->enum_type_plancher_haut_id) {
            8, 11, 16 => Configuration::TERRASSE,
            12, 13 => Configuration::RAMPANTS,
            default => match($this->enum_type_adjacence_id) {
                1, 2, 3, 5, 6 => Configuration::RAMPANTS,
                default => Configuration::PLANCHER,
            }
        };
    }

    public function type_structure(): ?TypePlancherHaut
    {
        return match ($this->enum_type_plancher_haut_id) {
            2 => TypePlancherHaut::PLAFOND_AVEC_OU_SANS_REMPLISSAGE,
            3 => TypePlancherHaut::PLAFOND_ENTRE_SOLIVES_METALLIQUES,
            4 => TypePlancherHaut::PLAFOND_ENTRE_SOLIVES_BOIS,
            5 => TypePlancherHaut::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => TypePlancherHaut::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES,
            7 => TypePlancherHaut::BARDEAUX_ET_REMPLISSAGE,
            8 => TypePlancherHaut::DALLE_BETON,
            9 => TypePlancherHaut::PLAFOND_BOIS_SUR_SOLIVES_BOIS,
            10 => TypePlancherHaut::PLAFOND_BOIS_SOUS_SOLIVES_BOIS,
            11 => TypePlancherHaut::PLAFOND_LOURD,
            12 => TypePlancherHaut::COMBLES_AMENAGES_SOUS_RAMPANT,
            13 => TypePlancherHaut::TOITURE_CHAUME,
            14 => TypePlancherHaut::PLAFOND_PATRE,
            15 => TypePlancherHaut::BAC_ACIER,
            default => null,
        };
    }

    public function mitoyennete(): Mitoyennete
    {
        return match ($this->enum_type_adjacence_id) {
            1 => Mitoyennete::EXTERIEUR,
            2 => Mitoyennete::ENTERRE,
            3 => Mitoyennete::VIDE_SANITAIRE,
            4 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
            5 => Mitoyennete::TERRE_PLEIN,
            6 => Mitoyennete::SOUS_SOL_NON_CHAUFFE,
            7 => Mitoyennete::LOCAL_NON_ACCESSIBLE,
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => Mitoyennete::LOCAL_NON_CHAUFFE,
            20 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
            22 => Mitoyennete::LOCAL_RESIDENTIEL,
        };
    }

    public function inertie(XMLRessource $ressource): Inertie
    {
        return $ressource->logement()->enveloppe->inertie_plancher_haut_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }

    public function etat_isolation(): ?EtatIsolation
    {
        return match ($this->enum_type_isolation_id) {
            2 => EtatIsolation::NON_ISOLE,
            3, 4, 5, 6, 7, 8 => EtatIsolation::ISOLE,
            default => null,
        };
    }

    public function type_isolation(): ?TypeIsolation
    {
        return match ($this->enum_type_isolation_id) {
            3 => TypeIsolation::ITI,
            4 => TypeIsolation::ITE,
            5 => TypeIsolation::ITR,
            6 => TypeIsolation::ITI_ITE,
            7 => TypeIsolation::ITI_ITR,
            8 => TypeIsolation::ITE_ITR,
            default => null
        };
    }
}
