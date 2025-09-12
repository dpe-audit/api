<?php

namespace App\Engine\Rules\Chauffage\Rendement;

use App\Engine\Input\Chauffage\InstallationInputRuleIterator;
use App\Engine\Tables\ChauffageTableValeurRepository;

final class RendementInstallationRule extends InstallationInputRuleIterator
{
    public function __construct(
        private ChauffageTableValeurRepository $repository
    ) {}

    /**
     * Facteur de couverture solaire
     */
    public function fch(): float
    {
        return $this->get("fch", function (): float {
            if (false === $this->item()->solaire_thermique()) {
                return 0;
            }
            return $this->item()->fch_saisi() ?? $this->repository->fch(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                type_batiment: $this->data()->batiment->type_batiment(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Fch non trouvées");
        });
    }
}
