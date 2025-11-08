<?php

namespace App\Legacy\Model;

/**
 * @property array<Mur> $mur_collection
 * @property array<PlancherBas> $plancher_bas_collection
 * @property array<PlancherHaut> $plancher_haut_collection
 * @property array<BaieVitree> $baie_vitree_collection
 * @property array<Porte> $porte_collection
 * @property array<PontThermique> $pont_thermique_collection
 * @property array<Ets> $ets_collection
 */
final class Enveloppe
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
        $mur_collection = [];
        foreach ($xml->mur_collection->mur ?? [] as $mur) {
            $mur_collection[] = Mur::from($mur);
        }

        $plancher_bas_collection = [];
        foreach ($xml->plancher_bas_collection->plancher_bas ?? [] as $plancherBas) {
            $plancher_bas_collection[] = PlancherBas::from($plancherBas);
        }

        $plancher_haut_collection = [];
        foreach ($xml->plancher_haut_collection->plancher_haut ?? [] as $plancherHaut) {
            $plancher_haut_collection[] = PlancherHaut::from($plancherHaut);
        }

        $baie_vitree_collection = [];
        foreach ($xml->baie_vitree_collection->baie_vitree ?? [] as $baieVitree) {
            $baie_vitree_collection[] = BaieVitree::from($baieVitree);
        }

        $porte_collection = [];
        foreach ($xml->porte_collection->porte ?? [] as $porte) {
            $porte_collection[] = Porte::from($porte);
        }

        $pont_thermique_collection = [];
        foreach ($xml->pont_thermique_collection->pont_thermique ?? [] as $pontThermique) {
            $pont_thermique_collection[] = PontThermique::from($pontThermique);
        }

        $ets_collection = [];
        foreach ($xml->ets_collection->ets ?? [] as $ets) {
            $ets_collection[] = Ets::from($ets);
        }

        return new self(
            inertie_plancher_bas_lourd: (bool)(int) $xml->inertie->inertie_plancher_bas_lourd,
            inertie_plancher_haut_lourd: (bool)(int) $xml->inertie->inertie_plancher_haut_lourd,
            inertie_paroi_verticale_lourd: (bool)(int) $xml->inertie->inertie_paroi_verticale_lourd,
            enum_classe_inertie_id: (int) $xml->inertie->enum_classe_inertie_id,
            mur_collection: $mur_collection,
            plancher_bas_collection: $plancher_bas_collection,
            plancher_haut_collection: $plancher_haut_collection,
            baie_vitree_collection: $baie_vitree_collection,
            porte_collection: $porte_collection,
            pont_thermique_collection: $pont_thermique_collection,
            ets_collection: $ets_collection
        );
    }

    /**
     * @return array<Paroi>
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

    public function match_ets(string $reference): ?Ets
    {
        return array_find($this->ets_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_mur(string $reference): ?Mur
    {
        return array_find($this->mur_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_plancher_bas(string $reference): ?PlancherBas
    {
        return array_find($this->plancher_bas_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_plancher_haut(string $reference): ?PlancherHaut
    {
        return array_find($this->plancher_haut_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_porte(string $reference): ?Porte
    {
        return array_find($this->porte_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_baie_vitree(string $reference): ?BaieVitree
    {
        return array_find($this->baie_vitree_collection, fn($item) => $item->match_reference($reference));
    }

    public function match_paroi(string $reference): ?Paroi
    {
        return array_find($this->parois(), fn($item) => $item->match_reference($reference));
    }
}
