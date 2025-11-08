<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Enum\{Energie, Mois, Usage};
use App\Engine\Table\ChauffageTableValeurRepository;

abstract class PerformanceAuxiliaireRule extends CommonSystemeRule
{
    public function __construct(protected ChauffageTableValeurRepository $repository) {}

    /**
     * Consommation finale de l'auxiliaire de chauffage en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->caux_generation() + $this->caux_distribution();
        });
    }

    /**
     * Consommation primaire de l'auxiliaire de chauffage en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->cef_aux() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emission de CO2 de l'auxiliaire de chauffage en kg/an
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
            $bch = $this->bch();
            $rdim = $this->rdim();
            $paux = $this->paux();
            $pn = $this->pn();
            return ($paux / 1000 * $bch * $rdim) / $pn;
        });
    }

    /**
     * Consommation de l'auxiliaire de distribution en kWh
     */
    public function caux_distribution(?Mois $mois = null): float
    {
        $key = $mois ? "caux_distribution::{$mois->value}" : 'caux_distribution';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois): float => $this->caux_distribution($mois));
            }
            return Mois::reduce(function (Mois $mois): float {
                $puissance_circulateur = $this->puissance_circulateur();
                $nref = $this->nref($mois);
                return $puissance_circulateur * $nref / 1000;
            });
        });
    }

    /**
     * Puissance des auxiliaires de génération exprimée en W
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
     * Puissance du circulateur exprimée en W
     */
    public function puissance_circulateur(): float
    {
        return $this->get('puissance_circulateur', function (): float {
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
     * Puissance nominale en chaud expriomée en kW
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
     * Débit nominal du circulateur exprimé en m³/h
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
     * Longueur du réseau de distribution exprimées en m
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
     * Chute de température du réseau de distribution exprimée en °C
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
     * Pertes de charge de l'émetteur exprimées en kPa
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
