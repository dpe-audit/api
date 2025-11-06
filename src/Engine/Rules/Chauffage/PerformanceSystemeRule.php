<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, TemperatureDistribution};
use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Chauffage\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Chauffage\Installation\Regulation\TypeIntermittence;
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Domain\Chauffage\Systeme\{Systeme, Configuration};
use App\Domain\Common\Enum\{Mois, Usage};
use App\Engine\Context;
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};
use App\Engine\Rules\Enveloppe\{WithDeperditionRule, WithInertieRule};

abstract class PerformanceSystemeRule extends PerformanceAuxiliaireRule
{
    use WithBatiment, WithBatimentRule, WithDeperditionRule, WithInertieRule;

    abstract public static function supports(Systeme $entity): bool;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->systemes()
            ->filter(fn(Systeme $entity) => static::supports($entity))
            ->values();
    }

    // * Données d'entrée

    public function installation_collective(): bool
    {
        return $this->item()->installation()->systemes()->has_generateur_collectif();
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->item()->generateur()->position()->reseau_chaleur?->contenu_co2();
    }

    public function annee_installation_generateur(): int
    {
        return $this->item()->generateur()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
    }

    public function type_distribution(): TypeDistribution
    {
        return $this->item()->reseau()->type_distribution;
    }

    public function isolation_reseau(): IsolationReseau
    {
        return $this->item()->reseau()->isolation ?? IsolationReseau::NON_ISOLE;
    }

    public function comptage_individuel(): bool
    {
        return $this->item()->installation()->comptage_individuel();
    }

    public function regulation_centrale(): bool
    {
        return $this->item()->installation()->regulation_centrale()->presence_regulation;
    }

    public function regulation_terminale(): bool
    {
        return $this->item()->installation()->regulation_terminale()->presence_regulation;
    }

    public function type_intermittence(): TypeIntermittence
    {
        return $this->get('type_intermittence', function (): TypeIntermittence {
            return TypeIntermittence::determine(
                regulation_centrale: $this->item()->installation()->regulation_centrale(),
                regulation_terminale: $this->item()->installation()->regulation_terminale(),
                chauffage_collectif: $this->systeme_collectif(),
            );
        });
    }

    /**
     * @return TemperatureDistribution[]
     */
    private function temperatures_distribution(): array
    {
        $values = $this->item()->emetteurs()->map(fn(Emetteur $entity) => $entity->temperature_distribution())->values();
        return array_unique($values);
    }

    // * Données intermédiaires

    public function bch(): float
    {
        $bch = $this->require(PerformanceChauffageRule::class)->bch();

        if ($this->installation_collective()) {
            if (in_array($this->configuration(), [Configuration::BASE, Configuration::RELEVE])) {
                return Mois::reduce(function (Mois $mois) use ($bch) {
                    $dht = $this->dht($mois);
                    $dh14 = $this->dh14($mois);
                    return $bch * (1 / ($dht / $dh14));
                });
            }
            return Mois::reduce(function (Mois $mois) use ($bch) {
                $dht = $this->dht($mois);
                $dh14 = $this->dh14($mois);
                return $bch * ($dht / $dh14);
            });
        }
        return $bch;
    }

    public function fch(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->fch();
    }

    public function pertes_generation(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation::{$mois->value}" : 'pertes_generation';
        return $this->get($key, function () use ($mois): float {
            $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
            return $rule->pertes_generation($mois) * ($this->rdim() / $rule->rdim());
        });
    }

    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation_recuperables::{$mois->value}" : 'pertes_generation_recuperables';
        return $this->get($key, function () use ($mois): float {
            $rule = $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur());
            return $rule->pertes_generation_recuperables($mois) * ($this->rdim() / $rule->rdim());
        });
    }

    // * Données de sortie
    /**
     * Consommation finale du système de chauffage en kWh/an
     */
    public function cef_ch(): float
    {
        return $this->get('cef_ch', function (): float {
            return $this->bch() * (1 - $this->fch()) * $this->ich() * $this->rdim();
        });
    }

    /**
     * Consommation primaire du système de chauffage en kWh/an
     */
    public function cep_ch(): float
    {
        return $this->get('cep_ch', function (): float {
            return $this->cef_ch() * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de chauffage en kg/an
     */
    public function eges_ch(): float
    {
        return $this->get('eges_ch', function (): float {
            if ($contenu_co2_reseau_chaleur = $this->contenu_co2_reseau_chaleur()) {
                return $this->cef_ch() * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ch() * match ($this->energie_generateur()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->energie_generateur()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * Puissance émise utile par le générateur en base exprimée en kW
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
    public function t(): float
    {
        return $this->get('t', function (): float {
            $dh14 = Mois::reduce(fn(Mois $mois) => $this->dh14($mois));
            return 14 - ($this->pe() * $dh14 / $this->bch());
        });
    }

    /**
     * Degré heure base T
     */
    public function dht(Mois $mois): float
    {
        return $this->get("dht::{$mois->value}", function () use ($mois): float {
            $nref = $this->nref($mois);
            $text = $this->text($mois);
            $tbase = $this->tbase();
            $t = $this->t();
            $x = 0.5 * (($t - $tbase) / ($text - $tbase));
            return $nref * ($text - $tbase) * pow($x, 5) * (14 - 25 * $x + 20 * pow($x, 2) - 5 * pow($x, 3));
        });
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
    public function ich(): float
    {
        return $this->get('ich', function (): float {
            $rd = $this->rd();
            $re = $this->re();
            $rg = $this->rg();
            $rr = $this->rr();
            return 1 / ($rd * $re * $rg * $rr);
        });
    }

    /**
     * Rendement de génération
     */
    abstract public function rg(): float;

    /**
     * Rendement de distribution
     */
    public function rd(): float
    {
        return $this->get('rd', function (): float {
            $values = [];
            foreach ($this->temperatures_distribution() as $temperature_distribution) {
                $values[] = $this->repository->rd(
                    type_distribution: $this->type_distribution(),
                    temperature_distribution: $temperature_distribution,
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
                cef_ch: $rule->cef_ch(),
                cep_ch: $rule->cep_ch(),
                eges_ch: $rule->eges_ch(),
                cef_aux: $rule->cef_aux(),
                cep_aux: $rule->cep_aux(),
                eges_aux: $rule->eges_aux(),
                i0: $rule->i0(),
                int: $rule->int(),
                ich: $rule->ich(),
                rd: $rule->rd(),
                re: $rule->re(),
                rg: $rule->rg(),
                rr: $rule->rr(),
            ));
        }
    }
}
