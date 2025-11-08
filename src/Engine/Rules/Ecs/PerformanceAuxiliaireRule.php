<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\{Energie, Mois, Usage};
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerformanceAuxiliaireRule extends DimensionnementSystemeRule
{
    public function __construct(protected EcsTableValeurRepository $repository) {}

    abstract public function pertes_distribution(): float;

    /**
     * Consommation finale des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->caux_generation() + $this->caux_circulateur() + $this->caux_traceur();
        });
    }

    /**
     * Consommation primaire des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->cef_aux() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 des auxiliaires d'eau chaude sanitaire en kg
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->cef_aux() * Energie::ELECTRICITE->facteur_eges(Usage::AUXILIAIRE);
        });
    }

    /**
     * Consommation de l'auxiliaire de génération en kWh/an
     */
    public function caux_generation(): float
    {
        return $this->get('caux_generation', function (): float {
            return ($this->paux() * $this->becs() * $this->rdim()) / $this->pn() / 1000;
        });
    }

    /**
     * Consommation du circulateur en kWh/an
     */
    public function caux_circulateur(): float
    {
        return $this->get('caux_circulateur', function (): float {
            if ($this->bouclage_reseau() === BouclageReseau::RESEAU_NON_BOUCLE) {
                return 0;
            }
            $nh = Mois::reduce(fn(Mois $mois): float => $mois->nh());
            $nh_puisage = $this->nh_puisage();
            $puissance_circulateur = $this->puissance_circulateur();
            $rdim = $this->rdim();
            return $nh_puisage * $puissance_circulateur + ($nh - $nh_puisage) * 20 * $rdim / 1000;
        });
    }

    /**
     * Consommation du traceur en kWh/an
     */
    public function caux_traceur(): float
    {
        return $this->get('caux_traceur', function (): float {
            return $this->bouclage_reseau() === BouclageReseau::RESEAU_TRACE
                ? 0.14 * $this->becs() * $this->rdim()
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
    public function puissance_hydraulique(): float
    {
        return $this->get('puissance_hydraulique', function (): float {
            $pertes_distribution = $this->pertes_distribution();
            $nh_puisage = $this->nh_puisage();
            $pertes_charge_bouclage = $this->pertes_charge_bouclage();
            return $pertes_distribution / (5.815 * $nh_puisage) * $pertes_charge_bouclage / 3.6;
        });
    }

    /**
     * Puissance électrique du circulateur en W
     */
    public function puissance_circulateur(): float
    {
        return $this->get('puissance_circulateur', function (): float {
            $puissance_hydraulique = $this->puissance_hydraulique();
            $efficacite_circulateur = $this->efficacite_circulateur();
            return \max(20, $puissance_hydraulique / $efficacite_circulateur);
        });
    }

    /**
     * Efficacité du circulateur
     */
    public function efficacite_circulateur(): float
    {
        return $this->get('efficacite_circulateur', function (): float {
            return \pow($this->puissance_hydraulique(), 0.324) / 15.3;
        });
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
