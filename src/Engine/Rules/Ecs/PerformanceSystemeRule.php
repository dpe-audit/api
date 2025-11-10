<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Mois, Scenario, Usage};
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

    public function pertes_stockage_integre_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
        return $rule->pertes_stockage_recuperables($scenario, $mois) * ($this->rdim() / $rule->rdim());
    }

    // * Données de sortie

    /**
     * Liste des consommations du système d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = parent::consommations();

            return $collection->with(...Scenario::each(fn(Scenario $scenario) => Consommation::create(
                scenario: $scenario,
                usage: Usage::ECS,
                energie: $this->energie_generateur()->to(),
                cef: $this->cef_ecs($scenario),
                cep: $this->cep_ecs($scenario),
                eges: $this->eges_ecs($scenario),
            )));
        });
    }

    /**
     * Consommation finale d'eau chaude sanitaire en kWh/an
     */
    public function cef_ecs(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_ecs', $scenario]), function () use ($scenario): float {
            return $this->becs($scenario) * (1 - $this->fecs()) * $this->iecs($scenario) * $this->rdim();
        });
    }

    /**
     * Consommation primaire d'eau chaude sanitaire en kWh/an
     */
    public function cep_ecs(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_ecs', $scenario]), function () use ($scenario): float {
            return $this->cef_ecs($scenario) * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'eau chaude sanitaire en kg/an
     */
    public function eges_ecs(Scenario $scenario): float
    {
        return $this->get(self::implode(['eges_ecs', $scenario]), function () use ($scenario): float {
            if ($contenu_co2_reseau_chaleur = $this->contenu_co2_reseau_chaleur()) {
                return $this->cef_ecs($scenario) * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ecs($scenario) * match ($this->energie_generateur()) {
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
    public function iecs(Scenario $scenario): float
    {
        return $this->get(self::implode(['iecs', $scenario]), function () use ($scenario): float {
            return 1 / array_product([$this->rd(), $this->rg($scenario), $this->rgs($scenario), $this->rs($scenario)]);
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
    public function rs(Scenario $scenario): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération/stockage
     */
    public function rgs(Scenario $scenario): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération
     */
    public function rg(Scenario $scenario): float
    {
        return 1;
    }

    /**
     * Pertes de stockage en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage', $mois]),
            fn(): float => $this->pertes_stockage_integre($mois) + $this->pertes_stockage_independant($mois)
        );
    }

    /**
     * Pertes de stockage récupérables en Wh
     */
    public function pertes_stockage_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage_recuperables', $mois]),
            fn(): float => $this->pertes_stockage_integre_recuperables($scenario, $mois) + $this->pertes_stockage_independant_recuperables($scenario, $mois)
        );
    }

    /**
     * Pertes de stockage indépendant en Wh
     */
    public function pertes_stockage_independant(?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage_independant', $mois]),
            fn(): float => $mois
                ? (67662 * \pow($this->volume_stockage(), 0.55)) / 12
                : (67662 * \pow($this->volume_stockage(), 0.55))
        );
    }

    /**
     * Pertes de stockage indépendant récupérables en Wh
     */
    public function pertes_stockage_independant_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage_independant_recuperables', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->pertes_stockage_independant_recuperables($scenario, $mois));
                }
                return $this->position_volume_chauffe_stockage()
                    ? 0.48 * $this->nref($scenario, $mois) * ($this->pertes_stockage_independant() / 8760)
                    : 0;
            }
        );
    }

    /**
     * Pertes de distribution en Wh
     */
    public function pertes_distribution(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution', $scenario, $mois]),
            fn(): float => array_sum([
                $this->pertes_distribution_ind_vc($scenario, $mois),
                $this->pertes_distribution_col_vc($scenario, $mois),
                $this->pertes_distribution_col_hvc($scenario, $mois),
            ])
        );
    }

    /**
     * Pertes mensuelles de distribution récupérables en Wh
     */
    public function pertes_distribution_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution_recuperables', $scenario, $mois]),
            fn(): float => 0.48 * $this->pertes_distribution($scenario, $mois) / 8760
        );
    }

    /**
     * Pertes de distribution individuelle en volume chauffé en Wh
     */
    public function pertes_distribution_ind_vc(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution_ind_vc', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $item): float => $this->pertes_distribution_ind_vc($scenario, $item));
                }
                $surface = $this->surface();
                $lvc = 0.2 * $surface * $this->rdim();
                return (0.5 * $lvc) / $surface * $this->becs($scenario, $mois) * 1000;
            }
        );
    }

    /**
     * Pertes de distribution collective en volume chauffé en Wh
     */
    public function pertes_distribution_col_vc(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution_col_vc', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->pertes_distribution_col_vc($scenario, $mois));
                }
                return $this->generateur_collectif() ? 0.112 * $this->becs($scenario, $mois) * 1000 * $this->rdim() : 0;
            }
        );
    }

    /**
     * Pertes de distribution collective hors volume chauffé en Wh
     */
    public function pertes_distribution_col_hvc(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution_col_hvc', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->pertes_distribution_col_hvc($scenario, $mois));
                }
                return $this->generateur_collectif() ? 0.028 * $this->becs($scenario, $mois) * 1000 * $this->rdim() : 0;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                iecs: $rule->iecs(Scenario::CONVENTIONNEL),
                rd: $rule->rd(),
                rg: $rule->rg(Scenario::CONVENTIONNEL),
                rgs: $rule->rgs(Scenario::CONVENTIONNEL),
                rs: $rule->rs(Scenario::CONVENTIONNEL),
                pertes_stockage: $rule->pertes_stockage_independant(),
                pertes_stockage_recuperables: $rule->pertes_stockage_independant_recuperables(Scenario::CONVENTIONNEL),
                pertes_distribution: $rule->pertes_distribution(Scenario::CONVENTIONNEL),
                pertes_distribution_recuperables: $rule->pertes_distribution_recuperables(Scenario::CONVENTIONNEL),
                consommations: $rule->consommations(),
            ));
        }
    }
}
