<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Domain\Enveloppe\PontThermique\Liaison\{TypeIsolation, TypeLiaison, TypePose};
use App\Domain\Enveloppe\PontThermique\PontThermique;
use App\Engine\Context;
use App\Engine\RuleIterator;
use App\Engine\Table\PontThermiqueTableValeurRepository;

/**
 * @extends RuleIterator<PontThermique>
 */
final class DeperditionPontThermiqueRule extends RuleIterator
{
    public function __construct(
        protected PontThermiqueTableValeurRepository $repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->ponts_thermiques()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function longueur(): float
    {
        return $this->item()->longueur();
    }

    public function kpt_saisi(): ?float
    {
        return $this->item()->kpt();
    }

    public function type_liaison(): TypeLiaison
    {
        return $this->item()->liaison()->type;
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->item()->liaison()->pont_thermique_partiel;
    }

    public function pont_thermique_negligeable(): bool
    {
        if ($this->item()->liaison()->mur->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if ($this->item()->liaison()->plancher_bas()?->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if ($this->item()->liaison()->plancher_haut()?->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if (false === $this->item()->liaison()->mur->inertie()->toBoolean()) {
            return true;
        }
        if (false === $this->item()->liaison()->plancher_haut()?->inertie()->toBoolean()) {
            return true;
        }
        if (false === $this->item()->liaison()->plancher_bas()?->inertie()->toBoolean()) {
            return true;
        }
        return false;
    }

    public function annee_construction_mur(): int
    {
        return current(array_filter([
            $this->item()->liaison()->mur->annee_renovation(),
            $this->item()->liaison()->mur->annee_construction(),
            $this->input()->batiment->annee_construction,
        ]));
    }

    public function isolation_mur(): bool
    {
        return $this->item()->liaison()->mur->isolation()->etat?->toBoolean()
            ?? $this->annee_construction_mur() >= 1975;
    }

    public function annee_construction_plancher(): int
    {
        return current(array_filter([
            $this->item()->liaison()->plancher_bas()->annee_renovation(),
            $this->item()->liaison()->plancher_bas()->annee_construction(),
            $this->item()->liaison()->plancher_haut()->annee_renovation(),
            $this->item()->liaison()->plancher_haut()->annee_construction(),
            $this->input()->batiment->annee_construction,
        ]));
    }

    public function isolation_plancher(): ?bool
    {
        if ($plancher = $this->item()->liaison()->plancher_bas()) {
            if ($plancher?->type_structure() === TypePlancherBas::PLANCHER_ENTREVOUS_ISOLANT) {
                return true;
            }
            if ($plancher?->isolation()->etat) {
                return $plancher->isolation()->etat->toBoolean();
            }
            if ($plancher->position()->mitoyennete === Mitoyennete::TERRE_PLEIN) {
                return $this->annee_construction_plancher() >= 2001;
            }
            return $this->annee_construction_plancher() >= 1975;
        }
        if ($plancher = $this->item()->liaison()->plancher_haut()) {
            return $plancher->isolation()->etat?->toBoolean() ?? $this->annee_construction_plancher() >= 1975;
        }
        return null;
    }

    public function type_isolation_mur(): ?TypeIsolation
    {
        if ($value = $this->item()->liaison()->mur->isolation()->type) {
            return TypeIsolation::from($value->value);
        }
        return $this->isolation_mur() ? TypeIsolation::ITI : null;
    }

    public function type_isolation_plancher(): ?TypeIsolation
    {
        if ($plancher = $this->item()->liaison()->plancher_bas()) {
            if ($value = $plancher->isolation()->type) {
                return TypeIsolation::from($value->value);
            }
            return $this->isolation_plancher() ? TypeIsolation::ITE : null;
        }
        if ($plancher = $this->item()->liaison()->plancher_haut()) {
            if ($value = $plancher->isolation()->type) {
                return TypeIsolation::from($value->value);
            }
            return $this->isolation_plancher() ? TypeIsolation::ITE : null;
        }
        return null;
    }

    public function largeur_dormant(): ?float
    {
        $values = [50];
        $values[] = $this->item()->liaison()->baie()?->menuiserie()?->largeur_dormant;
        $values[] = $this->item()->liaison()->baie()?->position()->double_fenetre?->menuiserie()?->largeur_dormant;
        $values[] = $this->item()->liaison()->porte()?->menuiserie()?->largeur_dormant;
        return max(array_filter($values));
    }

    public function presence_retour_isolation(): bool
    {
        return $this->item()->liaison()->baie()->menuiserie()?->presence_retour_isolation
            ?? $this->item()->liaison()->porte()->menuiserie()?->presence_retour_isolation
            ?? false;
    }

    public function type_pose(): TypePose
    {
        $enum = $this->item()->liaison()->baie()?->position()->type_pose
            ?? $this->item()->liaison()->porte()?->position()->type_pose;
        return $enum ? TypePose::from($enum->value) : TypePose::NU_INTERIEUR;
    }

    // * Données calculées

    /**
     * Déperdition thermique en W/K
     */
    public function pt(): float
    {
        return $this->get('pt', function (): float {
            if ($this->pont_thermique_negligeable()) {
                return 0;
            }
            $kpt = $this->kpt();
            $longueur = $this->longueur();
            $pont_thermique_partiel = $this->pont_thermique_partiel();
            return $kpt * $longueur * ($pont_thermique_partiel ? 0.5 : 1);
        });
    }

    /**
     * Valeur du pont thermique en W/K
     */
    public function kpt(): float
    {
        return $this->get('kpt', function () {
            return $this->kpt_saisi() ?? $this->repository->kpt(
                type_liaison: $this->type_liaison(),
                type_isolation_mur: $this->type_isolation_mur(),
                type_isolation_plancher: $this->type_isolation_plancher(),
                isolation_mur: $this->isolation_mur(),
                isolation_plancher: $this->isolation_plancher(),
                type_pose: $this->type_pose(),
                presence_retour_isolation: $this->presence_retour_isolation(),
                largeur_dormant: $this->largeur_dormant(),
            ) ?? throw new \DomainException('Valeur forfaitaire kpt non trouvée');
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
                k: $rule->kpt(),
                pt: $rule->pt()
            ));
        }
    }
}
