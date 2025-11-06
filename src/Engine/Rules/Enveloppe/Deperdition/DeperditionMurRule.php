<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Mur\{Mur, TypeDoublage, TypeMur};
use App\Domain\Enveloppe\Paroi\Isolation\{EtatIsolation, TypeIsolation};
use App\Domain\Enveloppe\Paroi\Performance;
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\MurTableValeurRepository;

/**
 * @extends DeperditionParoiRule<Mur>
 */
final class DeperditionMurRule extends DeperditionParoiRule
{
    use WithBatimentRule;

    // Lambda par défaut des murs isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;
    // Résistance additionnelle dûe à la présence d'un enduit sur une paroi ancienne
    final public const RESISTANCE_ENDUIT_PAROI_ANCIENNE = 0.7;

    public function __construct(
        private MurTableValeurRepository $repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->murs()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_structure(): ?TypeMur
    {
        return $this->item()->type_structure();
    }

    public function epaisseur_structure(): ?float
    {
        return $this->item()->epaisseur_structure();
    }

    public function type_doublage(): ?TypeDoublage
    {
        return $this->item()->type_doublage();
    }

    public function paroi_ancienne(): bool
    {
        return $this->item()->paroi_ancienne();
    }

    public function presence_enduit_isolant(): bool
    {
        return $this->item()->presence_enduit_isolant();
    }

    public function annee_construction(): int
    {
        return current(array_filter([
            $this->item()->annee_renovation(),
            $this->item()->annee_construction(),
            $this->input()->batiment->annee_construction,
        ]));
    }

    public function annee_construction_isolation(): int
    {
        if ($this->annee_isolation()) {
            return $this->annee_isolation();
        }
        if ($this->etat_isolation() === EtatIsolation::ISOLE) {
            return $this->annee_construction() <= 1974 ? 1976 : $this->annee_construction();
        }
        return $this->annee_construction();
    }

    public function isolation(): bool
    {
        return $this->item()->isolation()->etat?->toBoolean() ?? $this->annee_construction() < 1975;
    }

    public function etat_isolation(): ?EtatIsolation
    {
        return $this->item()->isolation()->etat;
    }

    public function type_isolation(): ?TypeIsolation
    {
        return $this->item()->isolation()->type;
    }

    public function annee_isolation(): ?int
    {
        return $this->item()->isolation()->annee_installation;
    }

    public function epaisseur_isolation(): ?float
    {
        return $this->item()->isolation()->epaisseur;
    }

    public function resistance_isolation(): ?float
    {
        return $this->item()->isolation()->resistance_thermique;
    }

    public function u_saisi(): ?float
    {
        return $this->item()->u();
    }

    public function u0_saisi(): ?float
    {
        return $this->item()->u0();
    }

    // * Données calculées

    /**
     * Coefficient de transmission thermique du mur non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function (): float {
            $u0 = $this->u0_saisi()
                ?? $this->repository->u0(
                    type_structure: $this->type_structure(),
                    epaisseur_structure: $this->epaisseur_structure(),
                    annee_construction: $this->annee_construction(),
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
            if ($this->u_saisi()) {
                return $this->u_saisi();
            }
            if ($this->etat_isolation() === EtatIsolation::NON_ISOLE) {
                return $this->u0();
            }
            if ($this->etat_isolation() === EtatIsolation::ISOLE) {
                if ($r = $this->resistance_isolation()) {
                    return 1 / (1 / $this->u0() + $r);
                }
                if ($e = $this->epaisseur_isolation()) {
                    return 1 / (1 / $this->u0() + $e / 1000 / self::LAMBDA_ISOLATION_DEFAUT);
                }
            }
            if (null === $u = $this->repository->u(
                zone_climatique: $this->zone_climatique(),
                effet_joule: $this->effet_joule(),
                annee_construction_isolation: $this->annee_construction_isolation(),
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
            return Performance::from_umur(umur: $this->u());
        });
    }

    /**
     * Coefficient de transmission thermique additionnel dû à la présence d'un enduit isolant
     * sur une paroi ancienne exprimé en W/m².K
     */
    public function u0_enduit_isolant(): float
    {
        return $this->get('u0_enduit_isolant', function (): float {
            if (null === $this->paroi_ancienne()) {
                return 0;
            }
            if (null === $this->presence_enduit_isolant()) {
                return 0;
            }
            return $this->paroi_ancienne() && $this->presence_enduit_isolant()
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
            return ($r_doublage = $this->type_doublage()?->resistance_thermique_doublage()) > 0
                ? 1 / $r_doublage
                : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                sdep: $rule->sdep(),
                b: $rule->b(),
                u0: $rule->u0(),
                u: $rule->u(),
                performance: $rule->performance(),
                dp: $rule->dp(),
            ));
        }
    }
}
