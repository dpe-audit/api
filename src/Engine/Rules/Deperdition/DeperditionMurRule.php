<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Mur\Isolation\EtatIsolation;
use App\Domain\Enveloppe\Mur\Performance;
use App\Domain\Enveloppe\Mur\Position\Mitoyennete;
use App\Engine\Input\Enveloppe\MurInputRuleIterator;
use App\Engine\Table\MurTableValeurRepository;

final class DeperditionMurRule extends MurInputRuleIterator
{
    // Lambda par défaut des murs isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;
    // Résistance additionnelle dûe à la présence d'un enduit sur une paroi ancienne
    final public const RESISTANCE_ENDUIT_PAROI_ANCIENNE = 0.7;

    public function __construct(
        private MurTableValeurRepository $repository,
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
     * Coefficient de transmission thermique du mur non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function (): float {
            $u0 = $this->item()->u0_saisi()
                ?? $this->repository->u0(
                    type_structure: $this->item()->type_structure(),
                    epaisseur_structure: $this->item()->epaisseur_structure(),
                    annee_construction: $this->item()->annee_construction(),
                )
                ?? throw new \DomainException('Valeur forfaitaire Umur non trouvée');

            $u0 += $this->u0_doublage();
            $u0 += $this->u0_enduit_isolant();
            return \min($u0, 2.5);
        });
    }

    /**
     * Coefficient de transmission thermique du mur exprimé en W/m².K
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
                annee_construction_isolation: $this->item()->annee_construction_isolation(),
            )) {
                throw new \DomainException('Valeur forfaitaire Umur non trouvée');
            }
            return \min($this->u0(), $u);
        });
    }

    /**
     * Etat de performance du mur
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_data(umur: $this->u());
        });
    }

    /**
     * Coefficient de transmission thermique additionnel dû à la présence d'un enduit isolant
     * sur une paroi ancienne exprimé en W/m².K
     */
    public function u0_enduit_isolant(): float
    {
        return $this->get('u0_enduit_isolant', function (): float {
            if (null === $this->item()->paroi_ancienne()) {
                return 0;
            }
            if (null === $this->item()->presence_enduit_isolant()) {
                return 0;
            }
            return $this->item()->paroi_ancienne() && $this->item()->presence_enduit_isolant()
                ? 1 / self::RESISTANCE_ENDUIT_PAROI_ANCIENNE
                : 0;
        });
    }
    /**
     * Coefficient de transmission thermique additionnel dû au doublage exprimé en W/m².K
     */
    public function u0_doublage(): float
    {
        return $this->get('u0_doublage', function (): float {
            return ($r_doublage = $this->item()->type_doublage()?->resistance_thermique_doublage()) > 0
                ? 1 / $r_doublage
                : 0;
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
