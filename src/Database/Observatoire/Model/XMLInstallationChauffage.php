<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Chauffage\Installation\Solaire\Usage;

/**
 * @property array<XMLEmetteurChauffage> $emetteur_chauffage_collection
 * @property array<XMLGenerateurChauffage> $generateur_chauffage_collection
 */
final class XMLInstallationChauffage extends XMLUniqueElement
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_chauffee,
        public readonly ?int $nombre_logement_echantillon,
        public readonly float $rdim,
        public readonly int $nombre_niveau_installation_ch,
        public readonly int $enum_cfg_installation_ch_id,
        public readonly ?float $ratio_virtualisation,
        public readonly ?float $coef_ifc,
        public readonly ?float $cle_repartition_ch,
        public readonly int $enum_type_installation_id,
        public readonly int $enum_methode_calcul_conso_id,
        public readonly ?int $enum_methode_saisie_fact_couv_sol_id,
        public readonly ?int $tv_facteur_couverture_solaire_id,
        public readonly ?float $fch_saisi,

        public readonly array $emetteur_chauffage_collection,
        public readonly array $generateur_chauffage_collection,

        public readonly float $besoin_ch,
        public readonly float $besoin_ch_depensier,
        public readonly ?float $production_ch_solaire,
        public readonly ?float $fch,
        public readonly float $conso_ch,
        public readonly float $conso_ch_depensier
    ) {
    }

    /**
     * XSD logement/installation_chauffage_collection/installation_chauffage
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            surface_chauffee: (float) $xml->donnee_entree->surface_chauffee,
            nombre_logement_echantillon: (int) $xml->donnee_entree->nombre_logement_echantillon ?: null,
            rdim: (float) $xml->donnee_entree->rdim,
            nombre_niveau_installation_ch: (int) $xml->donnee_entree->nombre_niveau_installation_ch,
            enum_cfg_installation_ch_id: (int) $xml->donnee_entree->enum_cfg_installation_ch_id,
            ratio_virtualisation: (float) $xml->donnee_entree->ratio_virtualisation ?: null,
            coef_ifc: (float) $xml->donnee_entree->coef_ifc ?: null,
            cle_repartition_ch: (float) $xml->donnee_entree->cle_repartition_ch ?: null,
            enum_type_installation_id: (int) $xml->donnee_entree->enum_type_installation_id,
            enum_methode_calcul_conso_id: (int) $xml->donnee_entree->enum_methode_calcul_conso_id,
            enum_methode_saisie_fact_couv_sol_id: (int) $xml->donnee_entree->enum_methode_saisie_fact_couv_sol_id ?: null,
            tv_facteur_couverture_solaire_id: (int) $xml->donnee_entree->tv_facteur_couverture_solaire_id ?: null,
            fch_saisi: (float) $xml->donnee_entree->fch_saisi ?: null,
            emetteur_chauffage_collection: XMLEmetteurChauffage::from_collection($xml->emetteur_chauffage_collection),
            generateur_chauffage_collection: XMLGenerateurChauffage::from_collection($xml->generateur_chauffage_collection),
            besoin_ch: (float) $xml->donnee_intermediaire->besoin_ch,
            besoin_ch_depensier: (float) $xml->donnee_intermediaire->besoin_ch_depensier,
            production_ch_solaire: (float)$xml->donnee_intermediaire->production_ch_solaire ?: null,
            fch: (float)$xml->donnee_intermediaire->fch ?: null,
            conso_ch: (float)$xml->donnee_intermediaire->conso_ch,
            conso_ch_depensier: (float)$xml->donnee_intermediaire->conso_ch_depensier
        );
    }

    /**
     * XSD logement/installation_chauffage_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->installation_chauffage as $item) {
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
        return $this->description ?? '-';
    }

    /**
     * En présence d'une installation avec appoint électrique dans la salle de bain, la surface couverte
     * par l'installation est déduite des surfaces couvertes par ces émetteurs.
     */
    public function surface(): float
    {
        $value = $this->surface_chauffee;
        foreach ($this->emetteur_chauffage_collection as $key => $emetteur_chauffage) {
            $value -= $emetteur_chauffage->surface_appoint_electrique_sdp();
        }
        return $value;
    }

    public function installation_collective(): bool
    {
        return $this->enum_type_installation_id === 2;
    }

    public function usage_solaire(): ?Usage
    {
        return \in_array($this->enum_cfg_installation_ch_id, [2, 7]) ? Usage::CHAUFFAGE : null;
    }

    public function fch_saisi(): ?float
    {
        return $this->fch_saisi;
    }

    /**
     * En l'absence d'émetteurs, on considère la présence d'un comptage individuel (émission directe)
     */
    public function comptage_individuel(): ?bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->comptage_individuel() !== null) {
                return $emetteur_chauffage->comptage_individuel();
            }
        }
        return true;
    }

    public function presence_regulation_centrale(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->presence_regulation_centrale()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_minimum_temperature(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->regulation_centrale_minimum_temperature()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_detection_presence(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->regulation_centrale_detection_presence()) {
                return true;
            }
        }
        return false;
    }

    public function presence_regulation_terminale(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->presence_regulation_terminale()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_minimum_temperature(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->regulation_terminale_minimum_temperature()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_detection_presence(): bool
    {
        foreach ($this->emetteur_chauffage_collection as $emetteur_chauffage) {
            if (true === $emetteur_chauffage->regulation_terminale_detection_presence()) {
                return true;
            }
        }
        return false;
    }
}
