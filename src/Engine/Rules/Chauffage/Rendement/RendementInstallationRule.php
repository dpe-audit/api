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

    /**
     * Inverse du rendement de l'installation
     */
    public function ich(): float
    {
        return $this->get('ich', function (): float {
            return array_sum(array_map(
                fn($item) => $item->ich() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement d'emission de l'installation
     */
    public function re(): float
    {
        return $this->get('re', function (): float {
            return array_sum(array_map(
                fn($item) => $item->re() * $item->rdim(),
                $this->item()->systemes()
            ));
        });
    }

    /**
     * Rendement de distribution de l'installation
     */
    public function rd(): float
    {
        return $this->get('rd', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rd() * $item->rdim(),
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
     * Rendement de régulation de l'installation
     */
    public function rr(): float
    {
        return $this->get('rr', function (): float {
            return array_sum(array_map(
                fn($item) => $item->rr() * $item->rdim(),
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
            fch: $this->fch(),
            ich: $this->ich(),
            re: $this->re(),
            rd: $this->rd(),
            rg: $this->rg(),
            rr: $this->rr(),
        ));
    }
}
