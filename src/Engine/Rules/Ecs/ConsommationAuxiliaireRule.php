<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\{Energie, Mois};
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Engine\Input\Ecs\SystemeInputRuleIterator;
use App\Engine\Table\EcsTableValeurRepository;

final class ConsommationAuxiliaireRule extends SystemeInputRuleIterator
{
    public function __construct(
        private EcsTableValeurRepository $repository,
    ) {}

    /**
     * Consommation finale des auxiliaires de distribution exprimée en kWh
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return $this->caux_generation() + $this->caux_circulateur() + $this->caux_traceur();
        });
    }

    /**
     * Consommation primaire des auxiliaires de distribution exprimée en kWh
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->cef() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 des auxiliaires de distribution exprimées en kg
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return $this->cef() * 0.069;
        });
    }

    /**
     * Consommation de l'auxiliaire de génération en kWh/an
     */
    public function caux_generation(): float
    {
        return $this->get('caux_generation', function (): float {
            $becs = $this->data()->ecs->becs();
            $rdim = $this->item()->rdim();
            $paux = $this->paux();
            $pn = $this->item()->generateur()->pn();
            return ($paux * $becs * $rdim) / $pn / 1000;
        });
    }

    /**
     * Consommation du circulateur exprimée en kWh/an
     */
    public function caux_circulateur(): float
    {
        return $this->get('caux_circulateur', function (): float {
            if ($this->item()->bouclage_reseau() === BouclageReseau::RESEAU_NON_BOUCLE) {
                return 0;
            }
            $nh = Mois::reduce(fn($carry, Mois $mois) => $carry += $mois->nh());
            $nh_puisage = $this->nh_puisage();
            $puissance_circulateur = $this->puissance_circulateur();
            $rdim = $this->item()->rdim();
            return $nh_puisage * $puissance_circulateur + ($nh - $nh_puisage) * 20 * $rdim / 1000;
        });
    }

    /**
     * Consommation annuelle du traceur exprimée en kWh/an
     */
    public function caux_traceur(): float
    {
        return $this->get('caux_traceur', function (): float {
            return $this->item()->bouclage_reseau() === BouclageReseau::RESEAU_TRACE
                ? 0.14 * $this->data()->ecs->becs() * $this->item()->rdim()
                : 0;
        });
    }

    /**
     * Puissance des auxiliaires de génération exprimée en W
     */
    public function paux(): float
    {
        return $this->get('paux', function (): float {
            if (false === $this->item()->generateur()->energie()->is_combustible()) {
                return 0;
            }
            if ($this->item()->generateur()->generateur_multi_batiment()) {
                return 0;
            }
            return $this->repository->paux(
                type_generateur: $this->item()->generateur()->type(),
                energie_generateur: $this->item()->generateur()->energie(),
                presence_ventouse: $this->item()->generateur()->presence_ventouse(),
                pn: $this->item()->generateur()->pn(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Paux non trouvées");
        });
    }

    /**
     * Puissance hydraulique de bouclage exprimée en W
     */
    public function puissance_hydraulique(): float
    {
        return $this->get('puissance_hydraulique', function (): float {
            $pertes_distribution = $this->item()->pertes_distribution();
            $nh_puisage = $this->nh_puisage();
            $pertes_charge_bouclage = $this->pertes_charge_bouclage();
            return $pertes_distribution / (5.815 * $nh_puisage) * $pertes_charge_bouclage / 3.6;
        });
    }

    /**
     * Puissance électrique du circulateur exprimée en W
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
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $mois->nj() * 5;
            });
        });
    }

    /**
     * Longueur du bouclage exprimée en m
     */
    public function longueur_bouclage(): float
    {
        return $this->get('longueur_bouclage', function (): float {
            $surface = $this->item()->installation()->surface();
            $niveaux = $this->item()->niveaux_desservis();
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
