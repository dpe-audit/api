<?php

namespace App\Engine\Rules\Performance;

use App\Domain\Common\Enum\Energie;
use App\Engine\Rule;
use App\Engine\Table\EclairageTableValeurRepository;

final class PerformanceEclairageRule extends Rule
{
    // Constante de puissance d'éclairage en W/m²
    public final const PUISSANCE_ECLAIRAGE = 1.4;
    // Constante de taux d'utilisation de l'éclairage
    public final const TAUX_UTILISATION = 0.9;

    public function __construct(
        private EclairageTableValeurRepository $repository,
    ) {}

    /**
     * Consommation finale d'éclairage en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            $cef = $this->nhecl();
            $cef *= self::PUISSANCE_ECLAIRAGE * self::TAUX_UTILISATION;
            $cef *= $this->data()->batiment->surface_habitable();
            $cef /= 1000;
            return $cef;
        });
    }

    /**
     * Consommation primaire d'éclairage en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->cef() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'éclairage en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return $this->cef() * 0.069;
        });
    }

    /**
     * Nombre d'heures d'éclairage par an
     */
    public function nhecl(): float
    {
        return $this->get("nhecl", function (): float {
            return $this->repository->nhecl($this->data()->batiment->zone_climatique())
                ?? throw new \DomainException('Valeur forfaitaires "nhecl" non trouvée');
        });
    }
}
