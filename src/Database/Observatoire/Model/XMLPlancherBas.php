<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;

final class XMLPlancherBas extends XMLParoiOpaque
{
    public function __construct(
        public readonly ?float $upb0_saisi,
        public readonly ?float $upb_saisi,
        public readonly int $enum_type_plancher_bas_id,
        public readonly ?int $tv_upb0_id,
        public readonly int $tv_upb_id,

        public readonly bool $calcul_ue,
        public readonly ?float $perimetre_ue,
        public readonly ?float $surface_ue,
        public readonly ?float $ue,
        public readonly float $upb,
        public readonly float $upb_final,
        public readonly float $upb0
    ) {}

    /**
     * XSD logement/enveloppe/plancher_bas_collection/plancher_bas
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            upb0_saisi: (float) $xml->donnee_entree->upb0_saisi ?: null,
            upb_saisi: (float) $xml->donnee_entree->upb_saisi ?: null,
            enum_type_plancher_bas_id: (int) $xml->donnee_entree->enum_type_plancher_bas_id,
            tv_upb0_id: (int) $xml->donnee_entree->tv_upb0_id ?: null,
            tv_upb_id: (int) $xml->donnee_entree->tv_upb_id,
            calcul_ue: (bool)(int) $xml->donnee_entree->calcul_ue,
            perimetre_ue: (float) $xml->donnee_entree->perimetre_ue ?: null,
            surface_ue: (float) $xml->donnee_entree->surface_ue ?: null,
            ue: (float) $xml->donnee_entree->ue ?: null,
            upb: (float) $xml->donnee_intermediaire->upb,
            upb_final: (float) $xml->donnee_intermediaire->upb_final,
            upb0: (float) $xml->donnee_intermediaire->upb0 ?: null
        ))->set($xml);
    }

    /**
     * XSD logement/enveloppe/plancher_bas_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->plancher_bas as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function type_structure(): ?TypePlancherBas
    {
        return match ($this->enum_type_plancher_bas_id) {
            2 => TypePlancherBas::PLANCHER_AVEC_OU_SANS_REMPLISSAGE,
            3 => TypePlancherBas::PLANCHER_ENTRE_SOLIVES_METALLIQUES,
            4 => TypePlancherBas::PLANCHER_ENTRE_SOLIVES_BOIS,
            5 => TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => TypePlancherBas::BARDEAUX_ET_REMPLISSAGE,
            7 => TypePlancherBas::VOUTAINS_SUR_SOLIVES_METALLIQUES,
            8 => TypePlancherBas::VOUTAINS_BRIQUES_OU_MOELLONS,
            9 => TypePlancherBas::DALLE_BETON,
            10 => TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_BOIS,
            11 => TypePlancherBas::PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON,
            12 => TypePlancherBas::PLANCHER_ENTREVOUS_ISOLANT,
            default => null,
        };
    }

    public function inertie(XMLRessource $ressource): Inertie
    {
        return $ressource->logement()->enveloppe->inertie_plancher_bas_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }
}
