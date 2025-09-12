<?php

namespace App\Database\Observatoire\Model;

use App\Database\Observatoire\Model\Sortie\XMLApportBesoin;
use App\Database\Observatoire\Model\Sortie\XMLConfortEte;
use App\Database\Observatoire\Model\Sortie\XMLCout;
use App\Database\Observatoire\Model\Sortie\XMLDeperdition;
use App\Database\Observatoire\Model\Sortie\XMLEFConso;
use App\Database\Observatoire\Model\Sortie\XMLEmissionGES;
use App\Database\Observatoire\Model\Sortie\XMLEPConso;
use App\Database\Observatoire\Model\Sortie\XMLProductionElectricite;
use App\Database\Observatoire\Model\Sortie\XMLQualiteIsolation;
use App\Database\Observatoire\Model\Sortie\XMLEnergie;

/**
 * @property $sortie_par_energie_collection array<XMLEnergie>
 */
final class XMLSortie
{
    public function __construct(
        public readonly XMLDeperdition $deperdition,
        public readonly XMLApportBesoin $apport_et_besoin,
        public readonly XMLEFConso $ef_conso,
        public readonly XMLEPConso $ep_conso,
        public readonly XMLEmissionGES $emission_ges,
        public readonly XMLCout $cout,
        public readonly XMLProductionElectricite $production_electricite,
        public readonly XMLConfortEte $confort_ete,
        public readonly XMLQualiteIsolation $qualite_isolation,
        /** @var array<XMLEnergie> */
        public readonly array $sortie_par_energie_collection,
    ) {}

    /**
     * XSD logement/sortie
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            deperdition: XMLDeperdition::from($xml->deperdition),
            apport_et_besoin: XMLApportBesoin::from($xml->apport_et_besoin),
            ef_conso: XMLEFConso::from($xml->ef_conso),
            ep_conso: XMLEPConso::from($xml->ep_conso),
            emission_ges: XMLEmissionGES::from($xml->emission_ges),
            cout: XMLCout::from($xml->cout),
            production_electricite: XMLProductionElectricite::from($xml->production_electricite),
            confort_ete: XMLConfortEte::from($xml->confort_ete),
            qualite_isolation: XMLQualiteIsolation::from($xml->qualite_isolation),
            sortie_par_energie_collection: XMLEnergie::from_collection($xml->sortie_par_energie_collection)
        );
    }
}
