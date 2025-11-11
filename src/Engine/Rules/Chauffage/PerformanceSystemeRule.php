<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Chauffage\Systeme\Configuration;
use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Mois, Scenario, Usage};
use App\Engine\Context;

abstract class PerformanceSystemeRule extends PerformanceAuxiliaireRule
{
    // * Données intermédiaires

    public function bch(Scenario $scenario): float
    {
        return $this->get(self::implode(['bch', $scenario]), function () use ($scenario): float {
            $bch = $this->require(PerformanceChauffageRule::class)->bch($scenario);

            if ($this->installation_collective()) {
                if (in_array($this->configuration(), [Configuration::BASE, Configuration::RELEVE])) {
                    return Mois::reduce(function (Mois $mois) use ($scenario, $bch) {
                        $dht = $this->dht($scenario, $mois);
                        $dh14 = $this->dh14($mois);
                        return $bch * (1 / ($dht / $dh14));
                    });
                }
                return Mois::reduce(function (Mois $mois) use ($scenario, $bch) {
                    $dht = $this->dht($scenario, $mois);
                    $dh14 = $this->dh14($mois);
                    return $bch * ($dht / $dh14);
                });
            }
            return $bch;
        });
    }

    public function pertes_generation(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_generation', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
                return $rule->pertes_generation($scenario, $mois) * ($this->rdim() / $rule->rdim());
            }
        );
    }

    public function pertes_generation_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_generation_recuperables', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
                return $rule->pertes_generation_recuperables($scenario, $mois) * ($this->rdim() / $rule->rdim());
            }
        );
    }

    // * Données de sortie

    /**
     * Liste des consommations du système de chauffage
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = parent::consommations();

            return $collection->with(...Scenario::each(fn(Scenario $scenario) => Consommation::create(
                scenario: $scenario,
                usage: Usage::CHAUFFAGE,
                energie: $this->energie_generateur()->to(),
                cef: $this->cef_ch($scenario),
                cep: $this->cep_ch($scenario),
                eges: $this->eges_ch($scenario),
            )));
        });
    }

    /**
     * Consommation finale du système de chauffage en kWh/an
     */
    public function cef_ch(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_ch', $scenario]), function () use ($scenario): float {
            return $this->bch($scenario) * (1 - $this->fch()) * $this->ich($scenario) * $this->rdim();
        });
    }

    /**
     * Consommation primaire du système de chauffage en kWh/an
     */
    public function cep_ch(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_ch', $scenario]), function () use ($scenario): float {
            return $this->cef_ch($scenario) * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de chauffage en kg/an
     */
    public function eges_ch(Scenario $scenario): float
    {
        return $this->get(self::implode(['eges_ch', $scenario]), function () use ($scenario): float {
            if ($contenu_co2_reseau_chaleur = $this->contenu_co2_reseau_chaleur()) {
                return $this->cef_ch($scenario) * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ch($scenario) * match ($this->energie_generateur()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->energie_generateur()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * Puissance émise utile par le générateur en base en kW
     */
    public function pe(): float
    {
        return $this->get('pe', function (): float {
            return $this->pn() * $this->rd() * $this->re() * $this->rr();
        });
    }

    /**
     * Température de dimensionnement
     */
    public function t(Scenario $scenario): float
    {
        return $this->get(self::implode(['t', $scenario]), function () use ($scenario): float {
            $dh14 = Mois::reduce(fn(Mois $mois) => $this->dh14($mois));
            return 14 - ($this->pe() * $dh14 / $this->bch($scenario));
        });
    }

    /**
     * Degré heure base T
     */
    public function dht(Scenario $scenario, Mois $mois): float
    {
        return $this->get(
            self::implode(['dht', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                $nref = $this->nref($scenario, $mois);
                $text = $this->text($mois);
                $tbase = $this->tbase();
                $t = $this->t($scenario);
                $x = 0.5 * (($t - $tbase) / ($text - $tbase));
                return $nref * ($text - $tbase) * pow($x, 5) * (14 - 25 * $x + 20 * pow($x, 2) - 5 * pow($x, 3));
            }
        );
    }

    /**
     * Facteur d'intermittence
     */
    public function int(): float
    {
        return $this->get('int', function (): float {
            $g = $this->gv() / $this->volume_reference();
            return $this->i0() / (1 + 0.1 * ($g - 1));
        });
    }

    /**
     * Coefficient d'intermittence
     */
    public function i0(): float
    {
        return $this->get("i0", function (): float {
            $values = [];
            foreach ($this->emetteurs() as $emetteur) {
                $values[] = $this->repository->i0(
                    type_batiment: $this->type_batiment(),
                    type_emission: $emetteur['type_emission'],
                    type_intermittence: $this->type_intermittence(),
                    regulation_terminale: $this->regulation_terminale(),
                    inertie_lourde: $this->inertie()->lourde(),
                    comptage_individuel: $this->comptage_individuel(),
                    chauffage_collectif: $this->systeme_collectif(),
                    chauffage_central: true,
                ) ?? throw new \DomainException('Valeur forfaitaire I0 non trouvée');
            }
            return array_sum($values) / count($values);
        });
    }

    /**
     * Inverse du rendement du système
     */
    public function ich(Scenario $scenario = Scenario::CONVENTIONNEL): float
    {
        return $this->get(self::implode(['ich', $scenario]), fn(): float => 1 / array_product([
            $this->rd(),
            $this->re(),
            $this->rg($scenario),
            $this->rr(),
        ]));
    }

    /**
     * Rendement de génération
     */
    abstract public function rg(Scenario $scenario): float;

    /**
     * Rendement de distribution
     */
    public function rd(): float
    {
        return $this->get('rd', function (): float {
            $temperature_distribution = $this->temperatures_distribution();
            if (count($temperature_distribution) === 0) {
                return 1;
            }
            $values = [];
            foreach ($temperature_distribution as $temperature_distribution) {
                $values[] = $this->repository->rd(
                    type_distribution: $this->type_distribution(),
                    temperature_distribution: $temperature_distribution,
                    presence_fluide_frigorigene: $this->presence_fluide_frigorigene(),
                    isolation_reseau: $this->isolation_reseau(),
                    reseau_collectif: $this->systeme_collectif(),
                ) ?? throw new \DomainException('Valeur forfaitaire Rd non trouvée');
            }
            return count($values) ? array_sum($values) / count($values) : 1;
        });
    }

    /**
     * Rendement d'émission
     */
    public function re(): float
    {
        return $this->get('re', function (): float {
            $values = [];
            foreach ($this->emetteurs() as $emetteur) {
                $values[] = $this->repository->re(
                    type_emission: $emetteur['type_emission'],
                    type_generateur: $this->type_generateur(),
                    label_generateur: $this->label_generateur(),
                ) ?? throw new \DomainException('Valeur forfaitaire Re non trouvée');
            }
            return array_sum($values) / count($values);
        });
    }

    /**
     * Rendement de régulation
     */
    public function rr(): float
    {
        return $this->get('rr', function (): float {
            $values = [];
            foreach ($this->emetteurs() as $emetteur) {
                $values[] = $this->repository->rr(
                    type_emission: $emetteur['type_emission'],
                    type_generateur: $this->type_generateur(),
                    label_generateur: $this->label_generateur(),
                    reseau_collectif: $this->systeme_collectif(),
                    presence_regulation_terminale: $this->regulation_terminale(),
                    presence_robinet_thermostatique: $emetteur['robinet_thermostatique'],
                ) ?? throw new \DomainException('Valeur forfaitaire Rr non trouvée');
            }
            return array_sum($values) / count($values);
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
                i0: $rule->i0(),
                int: $rule->int(),
                ich: $rule->ich(),
                rd: $rule->rd(),
                re: $rule->re(),
                rg: $rule->rg(Scenario::CONVENTIONNEL),
                rr: $rule->rr(),
                consommations: $rule->consommations(),
            ));
        }
    }
}
