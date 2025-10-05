<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Paroi\Isolation\EtatIsolation;
use App\Domain\Enveloppe\Paroi\{Mitoyennete, Performance};
use App\Engine\Input\Enveloppe\PlancherHautInputRuleIterator;
use App\Engine\Table\PlancherHautTableValeurRepository;

final class DeperditionPlancherHautRule extends PlancherHautInputRuleIterator
{
    // Lambda par défaut des planchers hauts isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;

    public function __construct(
        private PlancherHautTableValeurRepository $repository,
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
     * Coefficient de transmission thermique du plancher haut non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function (): float {
            $value = $this->item()->u0_saisi()
                ?? $this->repository->u0($this->item()->type_structure())
                ?? throw new \DomainException('Valeur forfaitaire U0 non trouvée');

            return \min($value, 2.5);
        });
    }

    /**
     * Coefficient de transmission thermique du plancher haut exprimé en W/m².K
     */
    public function u(): float
    {
        return $this->get('u', function (): float {
            if ($this->item()->u_saisi()) {
                return $this->item()->u_saisi();
            }
            if ($this->item()->etat_isolation() === EtatIsolation::NON_ISOLE) {
                return $this->u0();
            }
            if ($this->item()->etat_isolation() === EtatIsolation::ISOLE) {
                if ($r = $this->item()->resistance_isolation()) {
                    return 1 / (1 / $this->u0() + $r);
                }
                if ($e = $this->item()->epaisseur_isolation()) {
                    return 1 / (1 / $this->u0() + $e / 1000 / self::LAMBDA_ISOLATION_DEFAUT);
                }
            }
            if (null === $u = $this->repository->u(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                effet_joule: $this->data()->batiment->effet_joule(),
                configuration: $this->item()->configuration(),
                annee_construction_isolation: $this->item()->annee_construction_isolation(),
            )) {
                throw new \DomainException('Valeur forfaitaire Uph non trouvée');
            }
            return \min($this->u0(), $u);
        });
    }

    /**
     * Etat de performance du plancher haut
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_uph(
                uph: $this->u(),
                configuration: $this->item()->configuration(),
            );
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
            u0: $this->u0(),
            u: $this->u(),
            performance: $this->performance(),
            dp: $this->dp(),
        ));
    }
}
