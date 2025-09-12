<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Enum\Energie;
use App\Domain\Ventilation\Installation\TypeVentilation;
use App\Engine\Input\Ventilation\{GenerateurInputRuleIterator, InstallationInput};
use App\Engine\Table\VentilationTableValeurRepository;

final class ConsommationAuxiliaireRule extends GenerateurInputRuleIterator
{
    public function __construct(
        private VentilationTableValeurRepository $repository,
    ) {}

    /**
     * Consommation finale de l'auxiliaire de ventilation en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            $cef = 8760 * ($this->pvent_moy() / 1000) * $this->ratio_utilisation();
            $cef *= $this->item()->rdim();
            return $this->round($cef);
        });
    }

    /**
     * Consommation primaire de l'auxiliaire de ventilation en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->cef() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 de l'auxiliaire de ventilation en kg/an
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return $this->cef() * 0.064;
        });
    }

    /**
     * Débit volumique conventionnel à reprendre de l'auxiliaire de ventilation en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function () {
            return $this->repository->qvarep_conv(
                type_ventilation: TypeVentilation::VENTILATION_MECANIQUE,
                type_generateur: $this->item()->type(),
                type_vmc: $this->item()->type_vmc(),
                generateur_collectif: $this->item()->generateur_collectif(),
                presence_echangeur_thermique: $this->item()->presence_echangeur_thermique(),
                annee_installation: $this->item()->annee_installation(),
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
                type_generateur: $this->item()->type(),
                type_vmc: $this->item()->type_vmc(),
                generateur_collectif: $this->item()->generateur_collectif(),
                annee_installation: $this->item()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "ratio_utilisation" non trouvée');
        });
    }

    /**
     * Puissance moyenne de l'auxiliaire de ventilation en W
     */
    public function pvent_moy(): float
    {
        if ($this->data()->batiment->type_batiment() === TypeBatiment::IMMEUBLE) {
            $value = $this->pvent() * $this->qvarep_conv();
            return $value *= array_sum(array_map(
                fn(InstallationInput $item) => $item->surface(),
                $this->item()->installations()
            ));
        }
        return $this->get('pvent_moy', function () {
            return $this->repository->pvent_moy(
                type_generateur: $this->item()->type(),
                type_vmc: $this->item()->type_vmc(),
                generateur_collectif: $this->item()->generateur_collectif(),
                annee_installation: $this->item()->annee_installation(),
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
                type_generateur: $this->item()->type(),
                type_vmc: $this->item()->type_vmc(),
                generateur_collectif: $this->item()->generateur_collectif(),
                annee_installation: $this->item()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "pvent" non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            ratio_utilisation: $this->ratio_utilisation(),
            pvent_moy: $this->pvent_moy(),
            cef: $this->cef(),
            cep: $this->cep(),
            eges: $this->eges(),
        ));
    }
}
