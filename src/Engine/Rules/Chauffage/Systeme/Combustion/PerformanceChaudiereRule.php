<?php

namespace App\Engine\Rules\Chauffage\Systeme\Combustion;

use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Scenario, Usage};
use App\Engine\Rules\Chauffage\Systeme\PerformanceCombustionRule;

abstract class PerformanceChaudiereRule extends PerformanceCombustionRule
{
    public function supports(): bool
    {
        if (false === parent::supports()) {
            return false;
        }
        return $this->type_generateur()->is_chaudiere() || $this->type_generateur()->is_pac();
    }

    /**
     * @inheritDoc
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = parent::consommations();

            if (false === $this->type_generateur()->is_pac()) {
                return $collection;
            }
            return $collection->with(...Scenario::each(fn(Scenario $scenario) => Consommation::create(
                scenario: $scenario,
                usage: Usage::CHAUFFAGE,
                energie: $this->bienergie_generateur()->to(),
                cef: $this->cef_ch_partie_chaudiere($scenario),
                cep: $this->cep_ch_partie_chaudiere($scenario),
                eges: $this->eges_ch_partie_chaudiere($scenario),
            )));
        });
    }

    /**
     * @inheritDoc
     */
    public function cef_ch(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_ch', $scenario]), function () use ($scenario): float {
            return $this->type_generateur()->is_pac()
                ? parent::cef_ch($scenario) * $this->zone_climatique()->taux_couverture_pac()
                : parent::cef_ch($scenario);
        });
    }

    /**
     * Consommation d'énergie finale de la partie chaudière pour les PAC hybrides en kWh/an
     */
    public function cef_ch_partie_chaudiere(Scenario $scenario): float
    {
        return $this->get(
            self::implode(['cef_ch_partie_chaudiere', $scenario]),
            function () use ($scenario): float {
                return $this->type_generateur()->is_pac()
                    ? parent::cef_ch($scenario) * (1 - $this->zone_climatique()->taux_couverture_pac())
                    : 0;
            }
        );
    }

    /**
     * Consommation primaire de la partie chaudière pour les PAC hybrides en kWh/an
     */
    public function cep_ch_partie_chaudiere(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_ch_partie_chaudiere', $scenario]), function () use ($scenario): float {
            return $this->type_generateur()->is_pac()
                ? $this->cef_ch_partie_chaudiere($scenario) * $this->bienergie_generateur()->to()->facteur_energie_primaire()
                : 0;
        });
    }

    /**
     * Emissions de CO2 de la partie chaudière pour les PAC hybrides en kg/an
     */
    public function eges_ch_partie_chaudiere(Scenario $scenario): float
    {
        return $this->get(self::implode(['eges_ch_partie_chaudiere', $scenario]), function () use ($scenario): float {
            if (false === $this->type_generateur()->is_pac()) {
                return 0;
            }
            return $this->cef_ch_partie_chaudiere($scenario) * match ($this->bienergie_generateur()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->energie_generateur()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * @inheritDoc
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get(self::implode(['rg', $scenario]), function () use ($scenario): float {
            if ($this->type_generateur()->is_pac()) {
                $scop = $this->scop();
                $taux_couverture_partie_pac = $this->zone_climatique()->taux_couverture_pac();
                $taux_couverture_partie_chaudiere = 1 - $taux_couverture_partie_pac;
                $rg = parent::rg($scenario) * $taux_couverture_partie_chaudiere;
                $rg += $scop * $taux_couverture_partie_pac;
                return $rg;
            }
            return parent::rg($scenario);
        });
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn() * 100;
        $tfonc = $this->tfonc100();

        $qp = 100 - ($rpn + 0.1 * (70 - $tfonc));
        $qp /= $rpn + 0.1 * (70 - $tfonc);
        $qp *= $pn;

        return $qp;
    }
}
