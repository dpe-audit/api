<?php

namespace App\Engine\Rules\Chauffage\Dimensionnement;

use App\Engine\Input\Chauffage\GenerateurInputRuleIterator;
use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Tables\ChauffageTableValeurRepository;

final class DimensionnementGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        private readonly ChauffageTableValeurRepository $repository,
    ) {}

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_reduce($this->item()->systemes(), fn(float $s, SystemeInput $i) => $s + $i->rdim(), 0);
        });
    }

    /**
     * Puissance conventionnelle de chauffage exprimée en W
     */
    public function pch(): float
    {
        return $this->get('pch', function (): float {
            $tbase = $this->data()->batiment->tbase();
            $gv = $this->data()->enveloppe->gv();
            $rdim = $this->rdim();
            $pch = (1.2 * $gv * (19 - $tbase)) / (1000 * \pow(0.95, 3)) * 1000 * $rdim;

            return $this->item()->generateur_collectif()
                ? $pch * (1 / $this->ratio_proratisation())
                : $pch;
        });
    }

    /**
     * TODO
     */
    public function ratio_proratisation(): float
    {
        return 1;
    }

    /**
     * Puissance nominale conventionnelle exprimée en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function (): float {
            if (null !== $this->item()->pn_saisi()) {
                return $this->item()->pn_saisi();
            }
            if (false === $this->item()->type()->is_chaudiere()) {
                return $this->item()->pch();
            }
            if (false === $this->item()->type()->is_poele_bouilleur()) {
                return $this->item()->pch();
            }
            if (null === $pn = $this->repository->pn(
                position_chaudiere: $this->item()->position_chaudiere(),
                annee_installation_generateur: $this->item()->annee_installation(),
                pdim: $this->pdim(),
            )) {
                throw new \DomainException('Valeur forfaitaire Pn non trouvée');
            }
            return $pn;
        });
    }

    /**
     * Puissance de dimensionnement du générateur exprimée en kW
     */
    public function pdim(): float
    {
        return $this->get('pdim', function (): float {
            return ($pecs = $this->item()->generateur_mixte()?->pecs())
                ? max($pecs, $this->item()->pch())
                : $this->item()->pch();
        });
    }
}
