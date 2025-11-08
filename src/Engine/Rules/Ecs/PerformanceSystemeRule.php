<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\{Mois, Usage};
use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;

abstract class PerformanceSystemeRule extends PerformanceAuxiliaireRule
{
    use WithBatimentRule;

    public function pertes_stockage_integre(?Mois $mois = null): float
    {
        $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
        return $rule->pertes_stockage($mois) * ($this->rdim() / $rule->rdim());
    }

    public function pertes_stockage_integre_recuperables(?Mois $mois = null): float
    {
        $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
        return $rule->pertes_stockage_recuperables($mois) * ($this->rdim() / $rule->rdim());
    }

    /**
     * Consommation finale d'eau chaude sanitaire en kWh/an
     */
    public function cef_ecs(): float
    {
        return $this->get('cef_ecs', function (): float {
            return $this->becs() * (1 - $this->fecs()) * $this->iecs() * $this->rdim();
        });
    }

    /**
     * Consommation primaire d'eau chaude sanitaire en kWh/an
     */
    public function cep_ecs(): float
    {
        return $this->get('cep_ecs', function (): float {
            return $this->cef_ecs() * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'eau chaude sanitaire en kg/an
     */
    public function eges_ecs(): float
    {
        return $this->get('eges_ecs', function (): float {
            if ($contenu_co2_reseau_chaleur = $this->contenu_co2_reseau_chaleur()) {
                return $this->cef_ecs() * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ecs() * match ($this->energie_generateur()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->energie_generateur()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * Inverse du rendement du système
     */
    public function iecs(): float
    {
        return $this->get('iecs', function (): float {
            return 1 / array_product([$this->rd(), $this->rg(), $this->rgs(), $this->rs()]);
        });
    }

    /**
     * Rendement annuel de distribution
     */
    final public function rd(): float
    {
        return $this->get("rd", function () {
            return $this->repository->rd(
                production_volume_habitable: $this->position_volume_chauffe(),
                reseau_collectif: $this->generateur_collectif(),
                bouclage_reseau: $this->bouclage_reseau(),
                alimentation_contigue: $this->alimentation_contigue(),
            ) ?? throw new \RuntimeException('Valeur forfaitaire Rd non trouvée');
        });
    }

    /**
     * Rendement annuel de stockage - Ne s'applique qu'aux systèmes électriques
     */
    public function rs(): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération/stockage
     */
    public function rgs(): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération
     */
    public function rg(): float
    {
        return 1;
    }

    /**
     * Pertes de stockage en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage::{$mois->value}" : "pertes_stockage";
        return $this->get($key, function () use ($mois): float {
            return $this->pertes_stockage_integre($mois) + $this->pertes_stockage_independant($mois);
        });
    }

    /**
     * Pertes de stockage récupérables en Wh
     */
    public function pertes_stockage_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage_recuperables::{$mois->value}" : "pertes_stockage_recuperables";
        return $this->get($key, function () use ($mois): float {
            return $this->pertes_stockage_integre_recuperables($mois) + $this->pertes_stockage_independant_recuperables($mois);
        });
    }

    /**
     * Pertes de stockage indépendant en Wh
     */
    public function pertes_stockage_independant(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage_independant::{$mois->value}" : "pertes_stockage_independant";
        return $this->get($key, function () use ($mois): float {
            return $mois
                ? (67662 * \pow($this->volume_stockage(), 0.55)) / 12
                : (67662 * \pow($this->volume_stockage(), 0.55));
        });
    }

    /**
     * Pertes de stockage indépendant récupérables en Wh
     */
    public function pertes_stockage_independant_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage_independant_recuperables::{$mois->value}" : "pertes_stockage_independant_recuperables";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_stockage_independant_recuperables($item));
            }
            return $this->position_volume_chauffe_stockage()
                ? 0.48 * $this->nref($mois) * ($this->pertes_stockage_independant() / 8760)
                : 0;
        });
    }

    /**
     * Pertes de distribution en Wh
     */
    public function pertes_distribution(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution::{$mois->value}" : "pertes_distribution";
        return $this->get($key, function () use ($mois): float {
            $pertes = $this->pertes_distribution_ind_vc($mois);
            $pertes += $this->pertes_distribution_col_vc($mois);
            $pertes += $this->pertes_distribution_col_hvc($mois);
            return $pertes;
        });
    }

    /**
     * Pertes mensuelles de distribution récupérables en Wh
     */
    public function pertes_distribution_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution_recuperables::{$mois->value}" : "pertes_distribution_recuperables";
        return $this->get($key, function () use ($mois): float {
            return 0.48 * $this->pertes_distribution($mois) / 8760;
        });
    }

    /**
     * Pertes de distribution individuelle en volume chauffé en Wh
     */
    public function pertes_distribution_ind_vc(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution_ind_vc::{$mois->value}" : "pertes_distribution_ind_vc";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_distribution_ind_vc($item));
            }
            $surface = $this->surface();
            $lvc = 0.2 * $surface * $this->rdim();
            return (0.5 * $lvc) / $surface * $this->becs($mois) * 1000;
        });
    }

    /**
     * Pertes de distribution collective en volume chauffé en Wh
     */
    public function pertes_distribution_col_vc(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution_col_vc::{$mois->value}" : "pertes_distribution_col_vc";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_distribution_col_vc($item));
            }
            return $this->generateur_collectif() ? 0.112 * $this->becs($mois) * 1000 * $this->rdim() : 0;
        });
    }

    /**
     * Pertes de distribution collective hors volume chauffé en Wh
     */
    public function pertes_distribution_col_hvc(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution_col_hvc::{$mois->value}" : "pertes_distribution_col_hvc";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_distribution_col_hvc($item));
            }
            return $this->generateur_collectif() ? 0.028 * $this->becs($mois) * 1000 * $this->rdim() : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                cef_ecs: $rule->cef_ecs(),
                cep_ecs: $rule->cep_ecs(),
                eges_ecs: $rule->eges_ecs(),
                cef_aux: $rule->cef_aux(),
                cep_aux: $rule->cep_aux(),
                eges_aux: $rule->eges_aux(),
                rdim: $rule->rdim(),
                iecs: $rule->iecs(),
                rd: $rule->rd(),
                rg: $rule->rg(),
                rgs: $rule->rgs(),
                rs: $rule->rs(),
                pertes_stockage: $rule->pertes_stockage_independant(),
                pertes_stockage_recuperables: $rule->pertes_stockage_independant_recuperables(),
                pertes_distribution: $rule->pertes_distribution(),
                pertes_distribution_recuperables: $rule->pertes_distribution_recuperables(),
            ));
        }
    }
}
