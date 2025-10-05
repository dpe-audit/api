<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Paroi\Isolation\EtatIsolation;
use App\Domain\Enveloppe\Paroi\Isolation\TypeIsolation;

abstract class XMLParoiOpaque extends XMLParoi
{
    public readonly float $surface_paroi_opaque;
    public readonly ?bool $paroi_lourde;
    public readonly int $enum_methode_saisie_u0_id;
    public readonly int $enum_methode_saisie_u_id;
    public readonly int $enum_type_isolation_id;
    public readonly ?int $enum_periode_isolation_id;
    public readonly ?float $resistance_isolation;
    public readonly ?float $epaisseur_isolation;

    /**
     * XSD logement/enveloppe/mur_collection/mur
     * XSD logement/enveloppe/plancher_bas_collection/plancher_bas
     * XSD logement/enveloppe/plancher_haut_collection/plancher_haut
     */
    protected function set(\SimpleXMLElement $xml): self
    {
        $this->surface_paroi_opaque = (float) $xml->donnee_entree->surface_paroi_opaque;
        $this->paroi_lourde = (bool)(int) $xml->donnee_entree->paroi_lourde ?: null;
        $this->enum_methode_saisie_u0_id = (int) $xml->donnee_entree->enum_methode_saisie_u0_id;
        $this->enum_methode_saisie_u_id = (int) $xml->donnee_entree->enum_methode_saisie_u_id;
        $this->enum_type_isolation_id = (int) $xml->donnee_entree->enum_type_isolation_id;
        $this->enum_periode_isolation_id = (int) $xml->donnee_entree->enum_periode_isolation_id ?: null;
        $this->resistance_isolation = (float) $xml->donnee_entree->resistance_isolation ?: null;
        $this->epaisseur_isolation = (float) $xml->donnee_entree->epaisseur_isolation ?: null;

        return parent::set($xml);
    }

    public function surface(): float
    {
        return $this->surface_paroi_opaque;
    }

    public function epaisseur_isolation(): ?float
    {
        return $this->epaisseur_isolation ? $this->epaisseur_isolation * 10 : null;
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

    public function annee_isolation(XMLRessource $ressource): ?int
    {
        return match ($this->enum_periode_isolation_id) {
            1 => 1947,
            2 => 1974,
            3 => 1977,
            4 => 1982,
            5 => 1988,
            6 => 2000,
            7 => 2005,
            8 => 2012,
            9 => 2021,
            10 => $ressource->administratif->annee_etablissement(),
            default => null,
        };
    }
}
