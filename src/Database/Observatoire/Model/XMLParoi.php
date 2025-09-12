<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Lnc\Paroi\Isolation;
use App\Domain\Enveloppe\Lnc\TypeLnc;

abstract class XMLParoi extends XMLUniqueElement
{
    public readonly string $reference;
    public readonly ?string $reference_lnc;
    public readonly ?string $description;
    public readonly ?int $tv_coef_reduction_deperdition_id;
    public readonly ?float $surface_aiu;
    public readonly ?float $surface_aue;
    public readonly ?int $enum_cfg_isolation_lnc_id;
    public readonly int $enum_type_adjacence_id;
    public readonly float $b;

    /**
     * @inheritDoc
     */
    public function identifiers(): array
    {
        return [$this->reference];
    }

    /**
     * XSD logement/enveloppe/mur_collection/mur
     * XSD logement/enveloppe/plancher_bas_collection/plancher_bas
     * XSD logement/enveloppe/plancher_haut_collection/plancher_haut
     * XSD logement/enveloppe/baie_vitree_collection/baie_vitree
     * XSD logement/enveloppe/porte_collection/porte
     */
    protected function set(\SimpleXMLElement $xml): self
    {
        $this->reference = (string) $xml->donnee_entree->reference;
        $this->reference_lnc = (string) $xml->donnee_entree->reference_lnc ?: null;
        $this->description = (string) $xml->donnee_entree->description ?: null;
        $this->tv_coef_reduction_deperdition_id = (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null;
        $this->surface_aiu = (float) $xml->donnee_entree->surface_aiu ?: null;
        $this->surface_aue = (float) $xml->donnee_entree->surface_aue ?: null;
        $this->enum_cfg_isolation_lnc_id = (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id ?: null;
        $this->enum_type_adjacence_id = (int) $xml->donnee_entree->enum_type_adjacence_id;
        $this->b = (float) $xml->donnee_intermediaire->b;
        return $this;
    }

    abstract public function surface(): float;

    public function description(): string
    {
        return $this->description ?? 'Description non renseignée';
    }

    public function type_lnc(): ?TypeLnc
    {
        return match ($this->enum_type_adjacence_id) {
            8 => TypeLnc::GARAGE,
            9 => TypeLnc::CELLIER,
            10 => TypeLnc::ESPACE_TAMPON_SOLARISE,
            11 => TypeLnc::COMBLE_FORTEMENT_VENTILE,
            12 => TypeLnc::COMBLE_FAIBLEMENT_VENTILE,
            13 => TypeLnc::COMBLE_TRES_FAIBLEMENT_VENTILE,
            14 => TypeLnc::CIRCULATION_SANS_OUVERTURE_EXTERIEURE,
            15 => TypeLnc::CIRCULATION_AVEC_OUVERTURE_EXTERIEURE,
            16 => TypeLnc::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE,
            17 => TypeLnc::HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE,
            18 => TypeLnc::HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE,
            19 => TypeLnc::GARAGE_COLLECTIF,
            21 => TypeLnc::AUTRES,
            default => null,
        };
    }

    public function isolation_paroi_lnc(): ?Isolation
    {
        return match ($this->enum_cfg_isolation_lnc_id) {
            2, 4 => Isolation::NON_ISOLE,
            3, 5 => Isolation::ISOLE,
            default => null,
        };
    }

    public function orientation_baie_ets(): ?float
    {
        return match ($this->enum_cfg_isolation_lnc_id) {
            6, 9 => 0,
            7, 10 => 180,
            8, 11 => 90,
            default => null,
        };
    }

    public function has_espace_tampon_solarise(): bool
    {
        return $this->enum_type_adjacence_id === 10;
    }

    public function has_local_non_chauffe(): bool
    {
        return in_array($this->enum_type_adjacence_id, [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21]);
    }

    public function lnc_id(XMLRessource $xml): ?Id
    {
        if (false === $this->has_local_non_chauffe()) {
            return null;
        }
        if ($this->reference_lnc) {
            if ($ets = $xml->logement()->enveloppe->find_ets($this->reference_lnc)) {
                return $ets->id();
            }
        }
        return $this->id();
    }
}
