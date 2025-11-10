<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Mois, Scenario, Usage};
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerformanceAuxiliaireRule extends DimensionnementSystemeRule
{
    public function __construct(protected EcsTableValeurRepository $repository) {}

    abstract public function pertes_distribution(Scenario $scenario, ?Mois $mois = null): float;

    /**
     * Liste des consommations des auxiliaires de refroidissement
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
     * Consommation finale des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cef_aux(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_aux', $scenario]), function () use ($scenario): float {
            return $this->caux_generation($scenario) + $this->caux_circulateur($scenario) + $this->caux_traceur($scenario);
        });
    }

    /**
     * Consommation primaire des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cep_aux(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_aux', $scenario]), function () use ($scenario): float {
            return $this->cef_aux($scenario) * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 des auxiliaires d'eau chaude sanitaire en kg
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
        return $this->get(self::implode(['caux_generation', $scenario]), function () use ($scenario): float {
            return ($this->paux() / 1000 * $this->becs($scenario) * $this->rdim()) / $this->pn() / 1000;
        });
    }

    /**
     * Consommation du circulateur en kWh/an
     */
    public function caux_circulateur(Scenario $scenario): float
    {
        return $this->get(self::implode(['caux_circulateur', $scenario]), function () use ($scenario): float {
            if ($this->bouclage_reseau() === BouclageReseau::RESEAU_NON_BOUCLE) {
                return 0;
            }
            $nh = Mois::reduce(fn(Mois $mois): float => $mois->nh());
            $nh_puisage = $this->nh_puisage();
            $puissance_circulateur = $this->puissance_circulateur($scenario) / 1000;
            $rdim = $this->rdim();
            return $nh_puisage * $puissance_circulateur + ($nh - $nh_puisage) * 20 * $rdim;
        });
    }

    /**
     * Consommation du traceur en kWh/an
     */
    public function caux_traceur(Scenario $scenario): float
    {
        return $this->get(self::implode(['caux_traceur', $scenario]), function () use ($scenario): float {
            return $this->bouclage_reseau() === BouclageReseau::RESEAU_TRACE
                ? 0.14 * $this->becs($scenario) * $this->rdim()
                : 0;
        });
    }

    /**
     * Puissance des auxiliaires de génération en W
     */
    public function paux(): float
    {
        return $this->get('paux', function (): float {
            if (false === $this->energie_generateur()->is_combustible()) {
                return 0;
            }
            if ($this->generateur_multi_batiment()) {
                return 0;
            }
            return $this->repository->paux(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                presence_ventouse: $this->presence_ventouse(),
                pn: $this->pn(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Paux non trouvées");
        });
    }

    /**
     * Puissance hydraulique de bouclage en W
     */
    public function puissance_hydraulique(Scenario $scenario): float
    {
        return $this->get(self::implode(['puissance_hydraulique', $scenario]), function () use ($scenario): float {
            $pertes_distribution = $this->pertes_distribution($scenario);
            $nh_puisage = $this->nh_puisage();
            $pertes_charge_bouclage = $this->pertes_charge_bouclage();
            return $pertes_distribution / (5.815 * $nh_puisage) * $pertes_charge_bouclage / 3.6;
        });
    }

    /**
     * Puissance électrique du circulateur en W
     */
    public function puissance_circulateur(Scenario $scenario): float
    {
        return $this->get(self::implode(['puissance_circulateur', $scenario]), function () use ($scenario): float {
            $puissance_hydraulique = $this->puissance_hydraulique($scenario);
            $efficacite_circulateur = $this->efficacite_circulateur($scenario);
            return \max(20, $puissance_hydraulique / $efficacite_circulateur);
        });
    }

    /**
     * Efficacité du circulateur
     */
    public function efficacite_circulateur(Scenario $scenario): float
    {
        return $this->get(
            self::implode(['efficacite_circulateur', $scenario]),
            fn(): float => \pow($this->puissance_hydraulique($scenario), 0.324) / 15.3
        );
    }

    /**
     * Nombre d'heures de puisage annuel
     */
    public function nh_puisage(): float
    {
        return $this->get('nh_puisage', function (): float {
            return Mois::reduce(fn(Mois $mois): float => $mois->nj() * 5);
        });
    }

    /**
     * Longueur du bouclage exprimée en m
     */
    public function longueur_bouclage(): float
    {
        return $this->get('longueur_bouclage', function (): float {
            $surface = $this->surface();
            $niveaux = $this->niveaux_desservis();
            return 4 * sqrt($surface / $niveaux) + 6 * ($niveaux - 0.5);
        });
    }

    /**
     * Pertes de charge du bouclage de l'installation exprimées en kPa
     */
    public function pertes_charge_bouclage(): float
    {
        return $this->get('pertes_charge_bouclage', fn(): float => 0.2 * $this->longueur_bouclage() * 10);
    }
}
