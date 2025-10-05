<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Chauffage\Systeme\Configuration;
use App\Domain\Common\Enum\{Mois, Usage};
use App\Engine\Input\Chauffage\SystemeInputRuleIterator;

final class ConsommationSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Consommation finale du système de chauffage en kWh/an
     */
    public function cef_ch(): float
    {
        return $this->get('cef_ch', function (): float {
            $bch = $this->bch();
            $ich = $this->item()->ich();
            $fch = $this->item()->installation()->fch();
            $rdim = $this->item()->rdim();
            return $bch * (1 - $fch) * $ich * $rdim;
        });
    }

    /**
     * Consommation primaire du système de chauffage en kWh/an
     */
    public function cep_ch(): float
    {
        return $this->get('cep_ch', function (): float {
            return $this->cef_ch() * $this->item()->generateur()->energie()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de chauffage en kg/an
     */
    public function eges_ch(): float
    {
        return $this->get('eges_ch', function (): float {
            if ($contenu_co2_reseau_chaleur = $this->item()->generateur()->contenu_co2_reseau_chaleur()) {
                return $this->cef_ch() * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ch() * match ($this->item()->generateur()->energie()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->item()->generateur()->energie()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * @use BesoinChauffageRule
     */
    private function bch(): float
    {
        $bch = $this->data()->chauffage->bch();

        if ($this->item()->installation()->installation_collective()) {
            if (in_array($this->item()->configuration(), [Configuration::BASE, Configuration::RELEVE])) {
                return Mois::reduce(function (Mois $mois) use ($bch) {
                    $dht = $this->dht($mois);
                    $dh14 = $this->data()->batiment->dh14($mois);
                    return $bch * (1 / ($dht / $dh14));
                });
            }
            return Mois::reduce(function (Mois $mois) use ($bch) {
                $dht = $this->dht($mois);
                $dh14 = $this->data()->batiment->dh14($mois);
                return $bch * ($dht / $dh14);
            });
        }
        return $bch;
    }

    /**
     * Puissance émise utile par le générateur en base exprimée en kW
     */
    public function pe(): float
    {
        return $this->get('pe', function (): float {
            $pn = $this->item()->generateur()->pn();
            $rd = $this->item()->rd();
            $re = $this->item()->re();
            $rr = $this->item()->rr();
            return $pn * $rd * $re * $rr;
        });
    }

    /**
     * Température de dimensionnement
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            $dh14 = Mois::reduce(fn(Mois $mois) => $this->data()->batiment->dh14($mois));
            return 14 - ($this->pe() * $dh14 / $this->bch());
        });
    }

    /**
     * Degré heure base T
     */
    public function dht(Mois $mois): float
    {
        $key = "dht::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $nref = $this->data()->batiment->nref($mois);
            $text = $this->data()->batiment->text($mois);
            $tbase = $this->data()->batiment->tbase();
            $t = $this->t();
            $x = 0.5 * (($t - $tbase) / ($text - $tbase));
            return $nref * ($text - $tbase) * pow($x, 5) * (14 - 25 * $x + 20 * pow($x, 2) - 5 * pow($x, 3));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_ch: $this->cef_ch(),
            cep_ch: $this->cep_ch(),
            eges_ch: $this->eges_ch(),
        ));
    }
}
