<?php

namespace App\Engine\Rules\Chauffage;

use App\Engine\Table\ChauffageTableValeurRepository;

abstract class DimensionnementGenerateurRule extends CommonGenerateurRule
{
    public function __construct(protected readonly ChauffageTableValeurRepository $repository) {}

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum($this->rdim_systemes());
        });
    }

    /**
     * Puissance conventionnelle de chauffage en kW
     */
    public function pch(): float
    {
        return $this->get('pch', function (): float {
            $pch = $this->require(PerformanceChauffageRule::class)->pch() * $this->rdim();
            return $this->generateur_collectif() ? $pch * (1 / $this->ratio_proratisation()) : $pch;
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
     * Puissance nominale conventionnelle en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function (): float {
            if ($this->pn_saisi()) {
                return $this->pn_saisi();
            }
            if (false === $this->type_generateur()->is_chaudiere()) {
                return $this->pch();
            }
            if (false === $this->type_generateur()->is_poele_bouilleur()) {
                return $this->pch();
            }
            return $this->repository->pn(
                position_chaudiere: $this->position_chaudiere(),
                annee_installation_generateur: $this->annee_installation(),
                pdim: $this->pdim(),
            ) ?? throw new \DomainException('Valeur forfaitaire Pn non trouvée');
        });
    }

    /**
     * Puissance de dimensionnement du générateur en kW
     */
    public function pdim(): float
    {
        return $this->get('pdim', function (): float {
            return max($this->pecs(), $this->pch());
        });
    }
}
