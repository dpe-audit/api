<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Context;
use App\Engine\Table\VentilationTableValeurRepository;

final class PerformanceGenerateurRule extends CommonGenerateurRule
{
    public function __construct(
        private VentilationTableValeurRepository $repository,
    ) {}

    /**
     * Consommation finale de l'auxiliaire de ventilation en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return 8760 * ($this->pvent_moy() / 1000) * $this->ratio_utilisation() * $this->rdim();
        });
    }

    /**
     * Consommation primaire de l'auxiliaire de ventilation en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->cef_aux() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 de l'auxiliaire de ventilation en kg/an
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->cef_aux() * Energie::ELECTRICITE->facteur_eges(Usage::AUXILIAIRE);
        });
    }

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum($this->rdim_installations());
        });
    }

    /**
     * Débit volumique conventionnel à reprendre de l'auxiliaire de ventilation en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function () {
            return $this->repository->qvarep_conv(
                type_ventilation: $this->type_ventilation(),
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                presence_echangeur_thermique: $this->presence_echangeur_thermique(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "qvarep_conv" non trouvée');
        });
    }

    /**
     * Ratio du temps d'utilisation du mode mécanique de l'auxiliaire de ventilation
     */
    public function ratio_utilisation(): float
    {
        return $this->get('ratio_utilisation', function () {
            return $this->repository->ratio_utilisation(
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "ratio_utilisation" non trouvée');
        });
    }

    /**
     * Puissance moyenne de l'auxiliaire de ventilation en W
     */
    public function pvent_moy(): float
    {
        if ($this->type_batiment() === TypeBatiment::IMMEUBLE) {
            return  $this->pvent() * $this->qvarep_conv() * $this->surface();
        }
        return $this->get('pvent_moy', function () {
            return $this->repository->pvent_moy(
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "pvent_moy" non trouvée');
        });
    }

    /**
     * Puissance de l'auxiliaire de ventilation en W/(m³/h)
     */
    public function pvent(): float
    {
        return $this->get('pvent', function () {
            return $this->repository->pvent(
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "pvent" non trouvée');
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
                rdim: $this->rdim(),
                ratio_utilisation: $this->ratio_utilisation(),
                pvent_moy: $this->pvent_moy(),
                cef_aux: $this->cef_aux(),
                cep_aux: $this->cep_aux(),
                eges_aux: $this->eges_aux(),
            ));
        }
    }
}
