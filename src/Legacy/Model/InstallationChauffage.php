<?php

namespace App\Legacy\Model;

use App\Domain\Common\ValueObject\Id;
use App\Legacy\Utils\Normalizer;

/**
 * @property array<EmetteurChauffage> $emetteur_chauffage_collection
 * @property array<GenerateurChauffage> $generateur_chauffage_collection
 */
final class InstallationChauffage
{
    use WithId, WithDescription;

    private ?string $installation_sdb_id = null;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_chauffee,
        public readonly float $rdim,
        public readonly ?int $nombre_logement_echantillon,
        public readonly int $nombre_niveau_installation_ch,
        public readonly ?float $ratio_virtualisation,
        public readonly ?float $coef_ifc,
        public readonly ?float $cle_repartition_ch,
        public readonly ?float $fch_saisi,

        public readonly int $enum_cfg_installation_ch_id,
        public readonly int $enum_type_installation_id,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly ?int $enum_methode_saisie_fact_couv_sol_id,
        public readonly ?int $tv_facteur_couverture_solaire_id,

        public readonly array $emetteur_chauffage_collection,
        public readonly array $generateur_chauffage_collection,

        public readonly float $besoin_ch,
        public readonly float $besoin_ch_depensier,
        public readonly ?float $production_ch_solaire,
        public readonly ?float $fch,
        public readonly float $conso_ch,
        public readonly float $conso_ch_depensier
    ) {}

    /**
     * XPATH //installation_chauffage_collection/installation_chauffage
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        $emetteur_chauffage_collection = [];
        foreach ($xml->emetteur_chauffage_collection->emetteur_chauffage ?? [] as $item) {
            $emetteur_chauffage_collection[] = EmetteurChauffage::from($item);
        }

        $generateur_chauffage_collection = [];
        foreach ($xml->generateur_chauffage_collection->generateur_chauffage ?? [] as $item) {
            $generateur_chauffage_collection[] = GenerateurChauffage::from($item);
        }

        return new self(
            reference: Normalizer::referenceval((string) $xml->donnee_entree->reference),
            description: (string) $xml->donnee_entree->description ?: null,
            surface_chauffee: (float) $xml->donnee_entree->surface_chauffee,
            nombre_logement_echantillon: (int) $xml->donnee_entree->nombre_logement_echantillon ?: null,
            nombre_niveau_installation_ch: (int) $xml->donnee_entree->nombre_niveau_installation_ch,
            rdim: (float) $xml->donnee_entree->rdim,
            ratio_virtualisation: (float) $xml->donnee_entree->ratio_virtualisation ?: null,
            coef_ifc: (float) $xml->donnee_entree->coef_ifc ?: null,
            cle_repartition_ch: (float) $xml->donnee_entree->cle_repartition_ch ?: null,
            fch_saisi: (float) $xml->donnee_entree->fch_saisi ?: null,

            enum_cfg_installation_ch_id: (int) $xml->donnee_entree->enum_cfg_installation_ch_id,
            enum_type_installation_id: (int) $xml->donnee_entree->enum_type_installation_id,
            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            enum_methode_saisie_fact_couv_sol_id: (int) $xml->donnee_entree->enum_methode_saisie_fact_couv_sol_id ?: null,
            tv_facteur_couverture_solaire_id: (int) $xml->donnee_entree->tv_facteur_couverture_solaire_id ?: null,

            emetteur_chauffage_collection: $emetteur_chauffage_collection,
            generateur_chauffage_collection: $generateur_chauffage_collection,

            besoin_ch: (float) $xml->donnee_intermediaire->besoin_ch,
            besoin_ch_depensier: (float) $xml->donnee_intermediaire->besoin_ch_depensier,
            production_ch_solaire: (float)$xml->donnee_intermediaire->production_ch_solaire ?: null,
            fch: (float)$xml->donnee_intermediaire->fch ?: null,
            conso_ch: (float)$xml->donnee_intermediaire->conso_ch,
            conso_ch_depensier: (float)$xml->donnee_intermediaire->conso_ch_depensier
        );
    }

    public function installation_sdb_id(): string
    {
        return $this->installation_sdb_id ??= (string) Id::create();
    }

    public function match_generateur_hybride(GenerateurChauffage $generateur_chauffage): ?GenerateurChauffage
    {
        return array_find(
            $this->generateur_chauffage_collection,
            fn($item) => $item->match_generateur_hybride($generateur_chauffage)
        );
    }
}
