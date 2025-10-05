<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\PontThermique\Liaison\TypeLiaison;

final class XMLPontThermique extends XMLUniqueElement
{
    use WithDescription, WithReferences;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_1,
        public readonly ?string $reference_2,
        public readonly ?string $description,
        public readonly float $pourcentage_valeur_pont_thermique,
        public readonly float $l,
        public readonly int $enum_type_liaison_id,
        public readonly int $enum_methode_saisie_pont_thermique_id,
        public readonly ?float $k_saisi,
        public readonly ?int $tv_pont_thermique_id,

        public readonly float $k
    ) {}

    /**
     * XSD logement/enveloppe/pont_thermique_collection/pont_thermique
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            reference_1: (string) $xml->donnee_entree->reference_1 ?: null,
            reference_2: (string) $xml->donnee_entree->reference_2 ?: null,
            description: (string) $xml->donnee_entree->description ?: null,
            pourcentage_valeur_pont_thermique: (float) $xml->donnee_entree->pourcentage_valeur_pont_thermique,
            l: (float) $xml->donnee_entree->l,
            enum_type_liaison_id: (int) $xml->donnee_entree->enum_type_liaison_id,
            enum_methode_saisie_pont_thermique_id: (int) $xml->donnee_entree->enum_methode_saisie_pont_thermique_id,
            k_saisi: (float) $xml->donnee_entree->k_saisi ?: null,
            tv_pont_thermique_id: (int) $xml->donnee_entree->tv_pont_thermique_id ?: null,
            k: (float) $xml->donnee_intermediaire->k
        );
    }

    /**
     * XSD logement/enveloppe/pont_thermique_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->pont_thermique as $item) {
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

    public function longueur(): float
    {
        return $this->l;
    }

    public function kpt(): ?float
    {
        return $this->k_saisi ?? null;
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->pourcentage_valeur_pont_thermique > 0;
    }

    public function mur_id(XMLRessource $xml): ?Id
    {
        return $xml->logement()->enveloppe->find_mur($this->reference_1)?->id()
            ?? $xml->logement()->enveloppe->find_mur($this->reference_2)?->id()
            ?? null;
    }

    public function plancher_id(XMLRessource $xml): ?Id
    {
        return $xml->logement()->enveloppe->find_plancher_bas($this->reference_1)?->id()
            ?? $xml->logement()->enveloppe->find_plancher_bas($this->reference_2)?->id()
            ?? $xml->logement()->enveloppe->find_plancher_haut($this->reference_1)?->id()
            ?? $xml->logement()->enveloppe->find_plancher_haut($this->reference_2)?->id()
            ?? null;
    }

    public function ouverture_id(XMLRessource $xml): ?Id
    {
        return $xml->logement()->enveloppe->find_baie_vitree($this->reference_1)?->id()
            ?? $xml->logement()->enveloppe->find_baie_vitree($this->reference_2)?->id()
            ?? $xml->logement()->enveloppe->find_porte($this->reference_1)?->id()
            ?? $xml->logement()->enveloppe->find_porte($this->reference_2)?->id()
            ?? null;
    }

    public function type_liaison(): TypeLiaison
    {
        return match ($this->enum_type_liaison_id) {
            1 => TypeLiaison::PLANCHER_BAS_MUR,
            2 => TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR,
            3 => TypeLiaison::PLANCHER_HAUT_MUR,
            4 => TypeLiaison::REFEND_MUR,
            5 => TypeLiaison::MENUISERIE_MUR
        };
    }
}
