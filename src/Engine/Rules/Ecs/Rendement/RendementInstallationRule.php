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

    /**
     * Inverse du rendement de l'installation
     */
    public function iecs(): float
    {
        return $this->get('iecs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->iecs() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement de distribution de l'installation
     */
    final public function rd(): float
    {
        return $this->get('rd', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rd() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement de stockage de l'installation
     */
    public function rs(): float
    {
        return $this->get('rs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rs() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement de génération/stockage de l'installation
     */
    public function rgs(): float
    {
        return $this->get('rgs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rgs() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement de génération de l'installation
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rg() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            fecs: $this->fecs(),
            iecs: $this->iecs(),
            rd: $this->rd(),
            rg: $this->rg(),
            rgs: $this->rgs(),
            rs: $this->rs(),
        ));
    }
}
