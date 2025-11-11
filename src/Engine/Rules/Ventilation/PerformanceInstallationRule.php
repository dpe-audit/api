<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\VentilationTableValeurRepository;

final class PerformanceInstallationRule extends CommonInstallationRule
{
    use WithBatimentRule;

    public function __construct(
        private VentilationTableValeurRepository $repository,
    ) {}

    /**
     * Ratio de dimensionnement de l'installation
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->surface() / $this->surface_totale();
        });
    }

    /**
     * Déperdition thermique par renouvellement d'air due au système de ventilation par degré
     * d'écart entre l'intérieur et l'extérieur en W/K
     */
    public function hvent(): float
    {
        return $this->get('hvent', function () {
            $sh = $this->surface_reference();
            return 0.34 * $this->qvarep_conv() * $sh * $this->rdim();
        });
    }

    /**
     * Débit volumique conventionnel à reprendre en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function () {
            return $this->repository->qvarep_conv(
                type_ventilation: $this->type_installation(),
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                presence_echangeur_thermique: $this->presence_echangeur_thermique(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "qvarep_conv" non trouvée');
        });
    }

    /**
     * Débit volumique conventionnel à souffler en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        return $this->get('qvasouf_conv', function () {
            return $this->repository->qvasouf_conv(
                type_ventilation: $this->type_installation(),
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                presence_echangeur_thermique: $this->presence_echangeur_thermique(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "qvasouf_conv" non trouvée');
        });
    }

    /**
     * Somme des modules d'entrée d'air en m3/(h.m²)
     */
    public function smea_conv(): float
    {
        return $this->get('smea_conv', function () {
            return $this->repository->smea_conv(
                type_ventilation: $this->type_installation(),
                type_generateur: $this->type_generateur(),
                type_vmc: $this->type_vmc(),
                generateur_collectif: $this->generateur_collectif(),
                presence_echangeur_thermique: $this->presence_echangeur_thermique(),
                annee_installation: $this->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "smea_conv" non trouvée');
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
                qvarep_conv: $this->qvarep_conv(),
                qvasouf_conv: $this->qvasouf_conv(),
                smea_conv: $this->smea_conv(),
            ));
        }
    }
}
