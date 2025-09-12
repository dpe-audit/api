<?php

namespace App\Engine\Rules\Deperdition;

use App\Engine\Input\Ventilation\InstallationInputRuleIterator;
use App\Engine\Table\VentilationTableValeurRepository;

final class DeperditionSystemeVentilationRule extends InstallationInputRuleIterator
{
    public function __construct(
        private VentilationTableValeurRepository $repository
    ) {}

    /**
     * Déperdition thermique par renouvellement d'air due au système de ventilation par degré
     * d'écart entre l'intérieur et l'extérieur exprimées en W/K
     */
    public function hvent(): float
    {
        return $this->get('hvent', function () {
            $sh = $this->data()->batiment->surface_habitable();
            return 0.34 * $this->item()->qvarep_conv() * $sh * $this->item()->rdim();
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            hvent: $this->hvent(),
            qvarep_conv: $this->item()->qvarep_conv(),
            qvasouf_conv: $this->item()->qvasouf_conv(),
            smea_conv: $this->item()->smea_conv(),
        ));
    }
}
