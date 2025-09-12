<?php

namespace App\Engine\Rules\Apport\SurfaceSudEquivalente;

use App\Engine\Input\Enveloppe\LncBaieInputRuleIterator;
use App\Engine\Table\LncTableValeurRepository;

final class CoefficientTransparenceEtsBaieRule extends LncBaieInputRuleIterator
{
    public function __construct(
        private LncTableValeurRepository $repository,
    ) {}

    /**
     * Coefficient de transparence
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            return $this->repository->t(
                type_vitrage: $this->item()->type_vitrage(),
                materiau: $this->item()->materiau(),
                presence_rupteur_pont_thermique: $this->item()->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire t non trouvée');
        });
    }
}
