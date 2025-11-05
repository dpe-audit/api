<?php

namespace App\Legacy\Model;

use App\Legacy\Model\Sortie\ApportBesoin;
use App\Legacy\Model\Sortie\ConfortEte;
use App\Legacy\Model\Sortie\Cout;
use App\Legacy\Model\Sortie\Deperdition;
use App\Legacy\Model\Sortie\EFConso;
use App\Legacy\Model\Sortie\EmissionGES;
use App\Legacy\Model\Sortie\EPConso;
use App\Legacy\Model\Sortie\ProductionElectricite;
use App\Legacy\Model\Sortie\QualiteIsolation;
use App\Legacy\Model\Sortie\Energie;

/**
 * @property $sortie_par_energie_collection array<Energie>
 */
final class Sortie
{
    public function __construct(
        public readonly Deperdition $deperdition,
        public readonly ApportBesoin $apport_et_besoin,
        public readonly EFConso $ef_conso,
        public readonly EPConso $ep_conso,
        public readonly EmissionGES $emission_ges,
        public readonly Cout $cout,
        public readonly ProductionElectricite $production_electricite,
        public readonly ConfortEte $confort_ete,
        public readonly QualiteIsolation $qualite_isolation,
        /** @var array<Energie> */
        public readonly array $sortie_par_energie_collection,
    ) {}

    /**
     * XSD logement/sortie
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            deperdition: Deperdition::from($xml->deperdition),
            apport_et_besoin: ApportBesoin::from($xml->apport_et_besoin),
            ef_conso: EFConso::from($xml->ef_conso),
            ep_conso: EPConso::from($xml->ep_conso),
            emission_ges: EmissionGES::from($xml->emission_ges),
            cout: Cout::from($xml->cout),
            production_electricite: ProductionElectricite::from($xml->production_electricite),
            confort_ete: ConfortEte::from($xml->confort_ete),
            qualite_isolation: QualiteIsolation::from($xml->qualite_isolation),
            sortie_par_energie_collection: Energie::from_collection($xml->sortie_par_energie_collection)
        );
    }
}
