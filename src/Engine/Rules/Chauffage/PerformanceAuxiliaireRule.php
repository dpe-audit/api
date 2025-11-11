<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Mois, Scenario, Usage};
use App\Engine\Table\ChauffageTableValeurRepository;

abstract class PerformanceAuxiliaireRule extends CommonSystemeRule
{
    public function __construct(protected ChauffageTableValeurRepository $repository) {}

    /**
     * Liste des consommations des auxiliaires de chauffage
     */
    public function consommations(): ConsommationCollection
    {
        $collection = new ConsommationCollection();
        return $collection->with(...Scenario::each(fn(Scenario $scenario) => Consommation::create(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            cef: $this->cef_aux($scenario),
            cep: $this->cep_aux($scenario),
            eges: $this->eges_aux($scenario),
        )));
    }

    /**
     * Consommation finale de l'auxiliaire de chauffage en kWh/an
     */
    public function cef_aux(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_aux', $scenario]), function () use ($scenario): float {
            return $this->caux_generation($scenario) + $this->caux_distribution($scenario);
        });
    }

    /**
     * Consommation primaire de l'auxiliaire de chauffage en kWh/an
     */
    public function cep_aux(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_aux', $scenario]), function () use ($scenario): float {
            return $this->cef_aux($scenario) * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emission de CO2 de l'auxiliaire de chauffage en kg/an
     */
    public function eges_aux(Scenario $scenario): float
    {
        return $this->get(self::implode(['eges_aux', $scenario]), function () use ($scenario): float {
            return $this->cef_aux($scenario) * Energie::ELECTRICITE->facteur_eges(Usage::AUXILIAIRE);
        });
    }

    /**
     * Consommation de l'auxiliaire de génération en kWh/an
     */
    public function caux_generation(Scenario $scenario): float
    {
        return $this->get(
            self::implode(['caux_generation', $scenario]),
            function () use ($scenario): float {
                $bch = $this->bch($scenario);
                $rdim = $this->rdim();
                $paux = $this->paux() / 1000;
                $pn = $this->pn();
                return ($paux * $bch * $rdim) / $pn;
            }
        );
    }

    /**
     * Consommation de l'auxiliaire de distribution en kWh
     */
    public function caux_distribution(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['caux_distribution', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->caux_distribution($scenario, $mois));
                }
                $nref = $this->nref($scenario, $mois);
                $caux = $this->puissance_circulateur() * $nref;
                return $caux / 1000;
            }
        );
    }

    /**
     * Puissance des auxiliaires de génération en W
     */
    public function paux(): float
    {
        return $this->get("paux", function () {
            return $this->repository->paux(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                generateur_multi_batiment: $this->generateur_multi_batiment(),
                presence_ventouse: $this->presence_ventouse(),
                pn: $this->pn(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Paux non trouvées");
        });
    }

    /**
     * Puissance du circulateur en W
     */
    public function puissance_circulateur(): float
    {
        return $this->get('puissance_circulateur', function (): float {
            if (false === $this->reseau_distribution()) {
                return 0;
            }
            $debit_circulateur = $this->debit_circultateur();
            $pertes_charge = $this->pertes_charge();
            $surface = $this->surface_installation();
            $puissance_circulateur = 6.44;
            $puissance_circulateur *= pow($pertes_charge * ($debit_circulateur / max(1, $surface / 400)), 0.676);
            $puissance_circulateur += max(1, $surface / 400);
            return \max(30, $puissance_circulateur);
        });
    }

    /**
     * Puissance nominale en chaud en kW
     */
    public function pnc(): float
    {
        return $this->get('pnc', function (): float {
            $gv = $this->gv();
            $tbase = $this->tbase();
            return pow(10, -3) * $gv * (20 - $tbase);
        });
    }

    /**
     * Débit nominal du circulateur en m³/h
     */
    public function debit_circultateur(): float
    {
        return $this->get('debit_circultateur', function (): float {
            return ($chute_nominale_temperature = $this->chute_nominale_temperature())
                ? ($this->pnc() * $this->rdim()) / (1.163 * $chute_nominale_temperature)
                : 0;
        });
    }

    /**
     * Longueur du réseau de distribution en m
     */
    public function lem(): float
    {
        return $this->get('lem', function (): float {
            $fcot = $this->fcot();
            $niveaux_desservis = $this->niveaux_desservis();
            $surface = $this->surface_installation();
            return $niveaux_desservis && $surface
                ? 5 * $fcot * ($niveaux_desservis + \pow($surface / $niveaux_desservis, 0.5))
                : 0;
        });
    }

    /**
     * Chute de température du réseau de distribution en °C
     */
    public function chute_nominale_temperature(): float
    {
        return $this->get('chute_nominale_temperature', function (): float {
            return array_reduce($this->emetteurs(), function (float $carry, array $item): float {
                return max($carry, $item['temperature_distribution']?->chute_nominale_temperature() ?? 0);
            }, 0);
        });
    }

    /**
     * Pertes de charge de l'émetteur en kPa
     */
    public function pertes_charge(): float
    {
        return $this->get('pertes_charge', function (): float {
            return array_reduce($this->emetteurs(), function (float $max, array $item): float {
                return max($max, 0.15 * $this->lem() + $item['type']?->pertes_charge());
            }, 0);
        });
    }

    public function fcot(): float
    {
        return $this->get('fcot', function (): float {
            return array_reduce($this->emetteurs(), function (float $max, array $item): float {
                return max($max, $item['type']?->fcot() ?? 0);
            }, 0);
        });
    }
}
