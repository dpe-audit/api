<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Enveloppe\DoubleFenetre\{DoubleFenetre, TypeBaie};
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\TypeVitrage;
use App\Engine\{Context, RuleIterator};
use App\Engine\Table\DoubleFenetreTableValeurRepository;

/**
 * @extends RuleIterator<DoubleFenetre>
 */
final class SurfaceSudEquivalenteDoubleFenetre extends RuleIterator
{
    public function __construct(
        private DoubleFenetreTableValeurRepository $repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->doubles_fenetres()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function sw_saisi(): ?float
    {
        return $this->item()->sw();
    }

    public function type_baie(): TypeBaie
    {
        return $this->item()->type();
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->item()->vitrage()->type;
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        if (null === $this->item()->survitrage()) {
            return null;
        }
        return $this->item()->survitrage()->type ?? TypeSurvitrage::SURVITRAGE_SIMPLE;
    }

    public function type_pose(): TypePose
    {
        return $this->item()->position()->type_pose ?? TypePose::NU_EXTERIEUR;
    }

    public function presence_soubassement(): bool
    {
        return $this->item()->position()->presence_soubassement ?? false;
    }

    public function materiau(): Materiau
    {
        return $this->item()->menuiserie()?->materiau ?? Materiau::PVC;
    }

    // * Valeurs calculées

    /**
     * Proportion d’énergie solaire incidente solaire de la double fenêtre
     */
    public function sw(): float
    {
        return $this->get('sw', function (): float {
            if ($this->sw_saisi()) {
                return $this->sw_saisi();
            }
            return $this->repository->sw(
                type_baie: $this->type_baie(),
                type_pose: $this->type_pose(),
                presence_soubassement: $this->presence_soubassement(),
                materiau: $this->materiau(),
                type_vitrage: $this->type_vitrage(),
                type_survitrage: $this->type_survitrage(),
            ) ?? throw new \DomainException("Valeur forfaitaire sw non trouvée");
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
                sw: $rule->sw(),
            ));
        }
    }
}
