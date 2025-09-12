<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Exposition;
use App\Domain\Enveloppe\Niveau\InertieParoi;

/**
 * @property array<XMLMur> $mur_collection
 * @property array<XMLPlancherBas> $plancher_bas_collection
 * @property array<XMLPlancherHaut> $plancher_haut_collection
 * @property array<XMLBaieVitree> $baie_vitree_collection
 * @property array<XMLPorte> $porte_collection
 * @property array<XMLPontThermique> $pont_thermique_collection
 * @property array<XMLEts> $ets_collection
 */
final class XMLEnveloppe
{
    public function __construct(
        public readonly bool $inertie_plancher_bas_lourd,
        public readonly bool $inertie_plancher_haut_lourd,
        public readonly bool $inertie_paroi_verticale_lourd,
        public readonly int $enum_classe_inertie_id,
        public readonly array $mur_collection,
        public readonly array $plancher_bas_collection,
        public readonly array $plancher_haut_collection,
        public readonly array $baie_vitree_collection,
        public readonly array $porte_collection,
        public readonly array $pont_thermique_collection,
        public readonly array $ets_collection
    ) {}

    /**
     * XSD logement/enveloppe
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            inertie_plancher_bas_lourd: (bool)(int) $xml->inertie->inertie_plancher_bas_lourd,
            inertie_plancher_haut_lourd: (bool)(int) $xml->inertie->inertie_plancher_haut_lourd,
            inertie_paroi_verticale_lourd: (bool)(int) $xml->inertie->inertie_paroi_verticale_lourd,
            enum_classe_inertie_id: (int) $xml->inertie->enum_classe_inertie_id,
            mur_collection: XMLMur::from_collection($xml->mur_collection),
            plancher_bas_collection: XMLPlancherBas::from_collection($xml->plancher_bas_collection),
            plancher_haut_collection: XMLPlancherHaut::from_collection($xml->plancher_haut_collection),
            baie_vitree_collection: XMLBaieVitree::from_collection($xml->baie_vitree_collection),
            porte_collection: XMLPorte::from_collection($xml->porte_collection),
            pont_thermique_collection: XMLPontThermique::from_collection($xml->pont_thermique_collection),
            ets_collection: XMLEts::from_collection($xml->ets_collection)
        );
    }

    /**
     * @return array<XMLParoi>
     */
    public function parois(): array
    {
        return array_merge(
            $this->mur_collection,
            $this->plancher_bas_collection,
            $this->plancher_haut_collection,
            $this->baie_vitree_collection,
            $this->porte_collection
        );
    }

    public function find_ets(string $reference): ?XMLEts
    {
        foreach ($this->ets_collection as $ets) {
            if ($ets->match([$reference])) {
                return $ets;
            }
        }
        return null;
    }

    public function find_mur(string $reference): ?XMLMur
    {
        foreach ($this->mur_collection as $mur) {
            if ($mur->match([$reference])) {
                return $mur;
            }
        }
        return null;
    }

    public function find_plancher_bas(string $reference): ?XMLPlancherBas
    {
        foreach ($this->plancher_bas_collection as $plancherBas) {
            if ($plancherBas->match([$reference])) {
                return $plancherBas;
            }
        }
        return null;
    }

    public function find_plancher_haut(string $reference): ?XMLPlancherHaut
    {
        foreach ($this->plancher_haut_collection as $plancherHaut) {
            if ($plancherHaut->match([$reference])) {
                return $plancherHaut;
            }
        }
        return null;
    }

    public function find_porte(string $reference): ?XMLPorte
    {
        foreach ($this->porte_collection as $porte) {
            if ($porte->match([$reference])) {
                return $porte;
            }
        }
        return null;
    }

    public function find_baie_vitree(string $reference): ?XMLBaieVitree
    {
        foreach ($this->baie_vitree_collection as $baieVitree) {
            if ($baieVitree->match([$reference])) {
                return $baieVitree;
            }
        }
        return null;
    }

    public function inertie_paroi_verticale(): InertieParoi
    {
        return $this->inertie_paroi_verticale_lourd ? InertieParoi::LOURDE : InertieParoi::LEGERE;
    }

    public function inertie_plancher_haut(): InertieParoi
    {
        return $this->inertie_plancher_haut_lourd ? InertieParoi::LOURDE : InertieParoi::LEGERE;
    }

    public function inertie_plancher_bas(): InertieParoi
    {
        return $this->inertie_plancher_bas_lourd ? InertieParoi::LOURDE : InertieParoi::LEGERE;
    }

    public function exposition(XMLRessource $ressource): Exposition
    {
        return count($ressource->logement()->ventilation_collection)
            ? current($ressource->logement()->ventilation_collection)->exposition()
            : Exposition::EXPOSITION_MULTIPLE;
    }

    public function q4pa_conv(XMLRessource $ressource): ?float
    {
        return count($ressource->logement()->ventilation_collection)
            ? current($ressource->logement()->ventilation_collection)->q4pa_conv
            : null;
    }

    public function presence_brasseurs_air(XMLRessource $ressource): float
    {
        return $ressource->logement()->sortie->confort_ete->presence_brasseurs_air() ?? false;
    }
}
