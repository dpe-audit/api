<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

final class Porte extends Paroi
{
    public function __construct(
        public readonly ?string $reference_paroi,
        public readonly ?float $surface_porte,
        public readonly ?int $nb_porte,
        public readonly ?float $uporte_saisi,
        public readonly ?float $largeur_dormant,
        public readonly ?bool $presence_retour_isolation,
        public readonly bool $presence_joint,
        public readonly int $enum_type_porte_id,
        public readonly int $enum_type_pose_id,
        public readonly int $enum_methode_saisie_uporte_id,
        public readonly ?int $tv_uporte_id,
        public readonly float $uporte
    ) {}

    /**
     * XPATH //enveloppe/porte_collection/porte
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            reference_paroi: Normalizer::referenceval((string) $xml->donnee_entree->reference_paroi),
            surface_porte: (float) $xml->donnee_entree->surface_porte ?: null,
            nb_porte: (int) $xml->donnee_entree->nb_porte ?: null,
            uporte_saisi: (float) $xml->donnee_entree->uporte_saisi ?: null,
            largeur_dormant: (float) $xml->donnee_entree->largeur_dormant ?: null,
            presence_retour_isolation: (bool)(int) $xml->donnee_entree->presence_retour_isolation ?: null,
            presence_joint: (bool)(int) $xml->donnee_entree->presence_joint,
            enum_type_porte_id: (int) $xml->donnee_entree->enum_type_porte_id,
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            enum_methode_saisie_uporte_id: (int) $xml->donnee_entree->enum_methode_saisie_uporte_id,
            tv_uporte_id: (int) $xml->donnee_entree->tv_uporte_id ?: null,
            uporte: (float) $xml->donnee_intermediaire->uporte
        ))->set($xml);
    }

    public function surface(): float
    {
        return $this->surface_porte;
    }
}
