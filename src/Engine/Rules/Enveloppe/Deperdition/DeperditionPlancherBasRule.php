<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Paroi\Isolation\{EtatIsolation, TypeIsolation};
use App\Domain\Enveloppe\Paroi\{Mitoyennete, Performance};
use App\Domain\Enveloppe\PlancherBas\{PlancherBas, TypePlancherBas};
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\PlancherBasTableValeurRepository;

/**
 * @extends DeperditionParoiRule<PlancherBas>
 */
final class DeperditionPlancherBasRule extends DeperditionParoiRule
{
    use WithBatimentRule;

    // Lambda par défaut des planchers bas isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.042;

    public function __construct(private PlancherBasTableValeurRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->planchers_bas()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_structure(): ?TypePlancherBas
    {
        return $this->item()->type_structure();
    }

    public function surface_ue(): float
    {
        return $this->item()->position()->surface_ue;
    }

    public function perimetre_ue(): float
    {
        return $this->item()->position()->perimetre_ue;
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
     * @inheritDoc
     */
    public function u(): float
    {
        return $this->ue();
    }

    /**
     * Coefficient de transmission thermique du plancher bas non isolé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function (): float {
            return $this->u0_saisi()
                ?? $this->repository->u0($this->type_structure())
                ?? throw new \DomainException('Valeur forfaitaire U0 non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique du plancher haut en W/m².K
     */
    public function upb(): float
    {
        return $this->get('upb', function (): float {
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
            )) throw new \DomainException('Valeur forfaitaire Upb non trouvée');

            return \min($this->u0(), $u);
        });
    }

    /**
     * Coefficient de transmission thermique du plancher bas isolé en W/m².K
     */
    public function ue(): float
    {
        return $this->get('u', function (): float {
            if ($this->u_saisi()) {
                return $this->u_saisi();
            }
            $u = $this->upb();

            $u_final = \in_array($this->mitoyennete(), [
                Mitoyennete::TERRE_PLEIN,
                Mitoyennete::VIDE_SANITAIRE,
                Mitoyennete::SOUS_SOL_NON_CHAUFFE
            ]) ? $this->repository->ue(
                mitoyennete: $this->mitoyennete(),
                annee_construction: $this->annee_construction(),
                surface: $this->surface_ue(),
                perimetre: $this->perimetre_ue(),
                u: $u,
            ) : $u;

            return \min($u, $u_final);
        });
    }

    /**
     * Etat de performance du plancher bas
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_upb(upb: $this->u());
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
