<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\PlancherBas\Position\Mitoyennete as MitoyennetePlancherBas;
use App\Domain\Enveloppe\PlancherBas\TypePlancherBas;
use App\Domain\Enveloppe\PontThermique\Liaison\{TypeIsolation, TypeLiaison, TypePose};
use App\Domain\Enveloppe\PontThermique\PontThermique;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Deperdition\DeperditionPontThermiqueRule;

final class PontThermiqueInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly PontThermique $entity,
    ) {}

    public function longueur(): float
    {
        return $this->entity->longueur();
    }

    public function kpt_saisi(): ?float
    {
        return $this->entity->kpt();
    }

    public function type_liaison(): TypeLiaison
    {
        return $this->entity->liaison()->type;
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->entity->liaison()->pont_thermique_partiel;
    }

    public function pont_thermique_negligeable(): bool
    {
        if ($this->entity->liaison()->mur->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if ($this->entity->liaison()->plancher_bas()?->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if ($this->entity->liaison()->plancher_haut()?->type_structure()?->pont_thermique_negligeable()) {
            return true;
        }
        if (false === $this->entity->liaison()->mur->inertie()->toBoolean()) {
            return true;
        }
        if (false === $this->entity->liaison()->plancher_haut()?->inertie()->toBoolean()) {
            return true;
        }
        if (false === $this->entity->liaison()->plancher_bas()?->inertie()->toBoolean()) {
            return true;
        }
        return false;
    }

    public function annee_construction_mur(): int
    {
        return current(array_filter([
            $this->entity->liaison()->mur->annee_renovation(),
            $this->entity->liaison()->mur->annee_construction(),
            $this->context->data()->batiment->annee_construction(),
        ]));
    }

    public function isolation_mur(): bool
    {
        return $this->entity->liaison()->mur->isolation()->etat?->toBoolean()
            ?? $this->annee_construction_mur() >= 1975;
    }

    public function annee_construction_plancher(): int
    {
        return current(array_filter([
            $this->entity->liaison()->plancher_bas()->annee_renovation(),
            $this->entity->liaison()->plancher_bas()->annee_construction(),
            $this->entity->liaison()->plancher_haut()->annee_renovation(),
            $this->entity->liaison()->plancher_haut()->annee_construction(),
            $this->context->data()->batiment->annee_construction(),
        ]));
    }

    public function isolation_plancher(): ?bool
    {
        if ($plancher = $this->entity->liaison()->plancher_bas()) {
            if ($plancher?->type_structure() === TypePlancherBas::PLANCHER_ENTREVOUS_ISOLANT) {
                return true;
            }
            if ($plancher?->isolation()->etat) {
                return $plancher->isolation()->etat->toBoolean();
            }
            if ($plancher->position()->mitoyennete === MitoyennetePlancherBas::TERRE_PLEIN) {
                return $this->annee_construction_plancher() >= 2001;
            }
            return $this->annee_construction_plancher() >= 1975;
        }
        if ($plancher = $this->entity->liaison()->plancher_haut()) {
            return $plancher->isolation()->etat?->toBoolean() ?? $this->annee_construction_plancher() >= 1975;
        }
        return null;
    }

    public function type_isolation_mur(): ?TypeIsolation
    {
        if ($value = $this->entity->liaison()->mur->isolation()->type) {
            return TypeIsolation::from($value->value);
        }
        return $this->isolation_mur() ? TypeIsolation::ITI : null;
    }

    public function type_isolation_plancher(): ?TypeIsolation
    {
        if ($plancher = $this->entity->liaison()->plancher_bas()) {
            if ($value = $plancher->isolation()->type) {
                return TypeIsolation::from($value->value);
            }
            return $this->isolation_plancher() ? TypeIsolation::ITE : null;
        }
        if ($plancher = $this->entity->liaison()->plancher_haut()) {
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
        $values[] = $this->entity->liaison()->baie()?->menuiserie()?->largeur_dormant;
        $values[] = $this->entity->liaison()->baie()?->position()->double_fenetre?->menuiserie()?->largeur_dormant;
        $values[] = $this->entity->liaison()->porte()?->menuiserie()?->largeur_dormant;
        return max(array_filter($values));
    }

    public function presence_retour_isolation(): bool
    {
        return $this->entity->liaison()->baie()->menuiserie()?->presence_retour_isolation
            ?? $this->entity->liaison()->porte()->menuiserie()?->presence_retour_isolation
            ?? false;
    }

    public function type_pose(): TypePose
    {
        $enum = $this->entity->liaison()->baie()?->position()->type_pose
            ?? $this->entity->liaison()->porte()?->position()->type_pose;
        return $enum ? TypePose::from($enum->value) : TypePose::NU_INTERIEUR;
    }

    public function pt(): float
    {
        /** @var DeperditionPontThermiqueRule $rule */
        $rule = $this->requireIterator(DeperditionPontThermiqueRule::class, $this);
        return $rule->pt();
    }
}
