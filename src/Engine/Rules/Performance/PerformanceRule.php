<?php

namespace App\Engine\Rules\Performance;

use App\Domain\Common\Bilan\{Bilan, EtiquetteClimat, EtiquetteEnergie};
use App\Domain\Diagnostic\Diagnostic;
use App\Domain\Scenario\Etape\Etape;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};
use App\Engine\Rules\Chauffage\PerformanceChauffageRule;
use App\Engine\Rules\Eclairage\PerformanceEclairageRule;
use App\Engine\Rules\Ecs\PerformanceEcsRule;
use App\Engine\Rules\Refroidissement\PerformanceRefroidissementRule;
use App\Engine\Rules\Ventilation\PerformanceVentilationRule;
use App\Engine\Table\PerformanceTableValeurRepository;

final class PerformanceRule extends Rule
{
    use WithBatiment, WithBatimentRule;

    public function __construct(
        private PerformanceTableValeurRepository $repository
    ) {}

    /**
     * Consommation finale d'énergie en kWh/m²/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            $value = $this->require(PerformanceChauffageRule::class)->cef_ch()
                + $this->require(PerformanceChauffageRule::class)->cef_aux()
                + $this->require(PerformanceEcsRule::class)->cef_ecs()
                + $this->require(PerformanceEcsRule::class)->cef_aux()
                + $this->require(PerformanceRefroidissementRule::class)->cef_fr()
                + $this->require(PerformanceRefroidissementRule::class)->cef_aux()
                + $this->require(PerformanceVentilationRule::class)->cef_aux()
                + $this->require(PerformanceEclairageRule::class)->cef_ecl();
            return $value / $this->surface_reference();
        });
    }

    /**
     * Consommation primaire d'énergie en kWh/m²/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            $value = $this->require(PerformanceChauffageRule::class)->cep_ch()
                + $this->require(PerformanceChauffageRule::class)->cep_aux()
                + $this->require(PerformanceEcsRule::class)->cep_ecs()
                + $this->require(PerformanceEcsRule::class)->cep_aux()
                + $this->require(PerformanceRefroidissementRule::class)->cep_fr()
                + $this->require(PerformanceRefroidissementRule::class)->cep_aux()
                + $this->require(PerformanceVentilationRule::class)->cep_aux()
                + $this->require(PerformanceEclairageRule::class)->cep_ecl();
            return $value / $this->surface_reference();
        });
    }

    /**
     * Emisssions de CO2 exprimées en kg/m²/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            $value = $this->require(PerformanceChauffageRule::class)->eges_ch()
                + $this->require(PerformanceChauffageRule::class)->eges_aux()
                + $this->require(PerformanceEcsRule::class)->eges_ecs()
                + $this->require(PerformanceEcsRule::class)->eges_aux()
                + $this->require(PerformanceRefroidissementRule::class)->eges_fr()
                + $this->require(PerformanceRefroidissementRule::class)->eges_aux()
                + $this->require(PerformanceVentilationRule::class)->eges_aux()
                + $this->require(PerformanceEclairageRule::class)->eges_ecl();
            return $value / $this->surface_reference();
        });
    }

    /**
     * Etiquette énergie
     */
    public function etiquette_energie(): EtiquetteEnergie
    {
        return $this->get('etiquette_energie', function (): EtiquetteEnergie {
            return $this->repository->etiquette_energie(
                zone_climatique: $this->zone_climatique(),
                altitude: $this->altitude(),
                cep: $this->cep(),
                eges: $this->eges(),
            ) ?? throw new \DomainException("Etiquette énergie non trouvée");
        });
    }

    /**
     * Etiquette climat
     */
    public function etiquette_climat(): EtiquetteClimat
    {
        return $this->get('etiquette_climat', function (): EtiquetteClimat {
            return $this->repository->etiquette_climat(
                zone_climatique: $this->zone_climatique(),
                altitude: $this->altitude(),
                eges: $this->eges(),
            ) ?? throw new \DomainException("Etiquette climat non trouvée");
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        if (!$data instanceof Diagnostic && !$data instanceof Etape) {
            return;
        }
        $data->calcule($data->data()->with(
            bilan: Bilan::create(
                cef: $this->cef(),
                cep: $this->cep(),
                eges: $this->eges(),
                etiquette_energie: $this->etiquette_energie(),
                etiquette_climat: $this->etiquette_climat(),
            )
        ));
    }
}
