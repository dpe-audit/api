<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Porte\Performance;
use App\Domain\Enveloppe\Porte\Position\Mitoyennete;
use App\Engine\Input\Enveloppe\PorteInputRuleIterator;
use App\Engine\Table\PorteTableValeurRepository;

final class DeperditionPorteRule extends PorteInputRuleIterator
{
    public function __construct(
        private PorteTableValeurRepository $repository
    ) {}

    /**
     * Déperditions thermiques exprimées en W/K
     */
    public function dp(): float
    {
        return $this->get('dp', function (): float {
            return $this->sdep() * $this->u() * $this->b();
        });
    }

    /**
     * Surface déperditive en m²
     */
    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return $this->item()->mitoyennete() !== Mitoyennete::LOCAL_RESIDENTIEL
                ? $this->item()->surface()
                : 0;
        });
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): float
    {
        return $this->get('b', function (): float {
            if ($this->item()->mitoyennete() === Mitoyennete::LOCAL_NON_CHAUFFE) {
                return $this->item()->local_non_chauffe()?->b()
                    ?? $this->item()->local_non_chauffe()?->bver($this->item()->isolation())
                    ?? throw new \DomainException('Valeur b non calculée');
            }
            return $this->repository->b($this->item()->mitoyennete())
                ?? throw new \DomainException('Valeur forfaitaire b non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique exprimé en W/m².K
     */
    public function u(): float
    {
        return $this->get('u', function (): float {
            return $this->item()->u_saisi() ?? $this->repository->u(
                presence_sas: $this->item()->presence_sas(),
                isolation: $this->item()->isolation(),
                materiau: $this->item()->materiau(),
                type_vitrage: $this->item()->type_vitrage(),
                taux_vitrage: $this->item()->taux_vitrage(),
            ) ?? throw new \DomainException('Valeur forfaitaire Uporte non trouvée');
        });
    }

    /**
     * Etat de performance de la porte
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_data($this->u());
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            sdep: $this->sdep(),
            b: $this->b(),
            u: $this->u(),
            dp: $this->dp(),
            performance: $this->performance(),
        ));
    }
}
