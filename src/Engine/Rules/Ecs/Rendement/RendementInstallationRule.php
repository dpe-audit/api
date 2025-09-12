<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Engine\Input\Ecs\InstallationInputRuleIterator;
use App\Engine\Table\EcsTableValeurRepository;

final class RendementInstallationRule extends InstallationInputRuleIterator
{
    public function __construct(
        private EcsTableValeurRepository $repository
    ) {}

    /**
     * Facteur de couverture solaire
     */
    public function fecs(): float
    {
        return $this->get("fecs", function () {
            if (null === $this->item()->solaire_thermique()) {
                return 0;
            }
            return $this->item()->fecs_saisi() ?? $this->repository->fecs(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                type_batiment: $this->data()->batiment->type_batiment(),
                usage_solaire: $this->item()->usage_solaire_thermique(),
                annee_installation: $this->item()->annee_installation_solaire_thermique(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Fecs non trouvées");
        });
    }
}
