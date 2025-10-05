<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\Input\Ventilation\InstallationInputRuleIterator;
use App\Engine\Table\VentilationTableValeurRepository;

final class DebitVentilationRule extends InstallationInputRuleIterator
{
    public function __construct(
        private VentilationTableValeurRepository $repository,
    ) {}

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function () {
            return $this->repository->qvarep_conv(
                type_ventilation: $this->item()->type_installation(),
                type_generateur: $this->item()->generateur()->type(),
                type_vmc: $this->item()->generateur()->type_vmc(),
                generateur_collectif: $this->item()->generateur()->generateur_collectif(),
                presence_echangeur_thermique: $this->item()->generateur()->presence_echangeur_thermique(),
                annee_installation: $this->item()->generateur()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "qvarep_conv" non trouvée');
        });
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        return $this->get('qvasouf_conv', function () {
            return $this->repository->qvasouf_conv(
                type_ventilation: $this->item()->type_installation(),
                type_generateur: $this->item()->generateur()->type(),
                type_vmc: $this->item()->generateur()->type_vmc(),
                generateur_collectif: $this->item()->generateur()->generateur_collectif(),
                presence_echangeur_thermique: $this->item()->generateur()->presence_echangeur_thermique(),
                annee_installation: $this->item()->generateur()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "qvasouf_conv" non trouvée');
        });
    }

    /**
     * Somme des modules d'entrée d'air exprimée en m3/(h.m²)
     */
    public function smea_conv(): float
    {
        return $this->get('smea_conv', function () {
            return $this->repository->smea_conv(
                type_ventilation: $this->item()->type_installation(),
                type_generateur: $this->item()->generateur()->type(),
                type_vmc: $this->item()->generateur()->type_vmc(),
                generateur_collectif: $this->item()->generateur()->generateur_collectif(),
                presence_echangeur_thermique: $this->item()->generateur()->presence_echangeur_thermique(),
                annee_installation: $this->item()->generateur()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire "smea_conv" non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            qvarep_conv: $this->qvarep_conv(),
            qvasouf_conv: $this->qvasouf_conv(),
            smea_conv: $this->smea_conv(),
        ));
    }
}
