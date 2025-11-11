<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\DoubleFenetre\{DoubleFenetre, TypeBaie};
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\{NatureGazLame, TypeVitrage};
use App\Engine\Context;
use App\Engine\RuleIterator;
use App\Engine\Table\DoubleFenetreTableValeurRepository;

/**
 * @extends RuleIterator<DoubleFenetre>
 */
final class DeperditionDoubleFenetreRule extends RuleIterator
{
    public function __construct(
        private DoubleFenetreTableValeurRepository $repository
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

    public function ug_saisi(): ?float
    {
        return $this->item()->ug();
    }

    public function uw_saisi(): ?float
    {
        return $this->item()->uw();
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

    public function inclinaison(): float
    {
        return $this->item()->position()->inclinaison;
    }

    public function presence_soubassement(): bool
    {
        return $this->item()->position()->presence_soubassement ?? false;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->item()->menuiserie()?->presence_rupteur_pont_thermique ?? false;
    }

    public function materiau(): Materiau
    {
        return $this->item()->menuiserie()?->materiau ?? Materiau::PVC;
    }

    public function epaisseur_lame(): float
    {
        if ($this->item()->vitrage()->epaisseur_lame) {
            return $this->item()->vitrage()->epaisseur_lame;
        }
        if ($this->item()->vitrage()->type === TypeVitrage::SIMPLE_VITRAGE) {
            return $this->item()->survitrage() !== null
                ? $this->item()->survitrage()->epaisseur_lame ?? 6
                : 0;
        }
        return $this->item()->vitrage()->type->vitrage_complexe() ? 6 : 0;
    }

    public function nature_lame(): ?NatureGazLame
    {
        return $this->item()->vitrage()->nature_lame
            ?? $this->item()->vitrage()->type->vitrage_complexe() ? NatureGazLame::AIR : null;
    }

    // * Valeurs calculées

    /**
     * Coefficient de transmission thermique du vitrage en W/m².K
     */
    public function ug(): float
    {
        return $this->get('ug', function (): float {
            return $this->ug_saisi() ?? $this->repository->ug(
                type_baie: $this->type_baie(),
                type_vitrage: $this->type_vitrage(),
                nature_gaz_lame: $this->nature_lame(),
                inclinaison_vitrage: $this->inclinaison(),
                epaisseur_lame_air: $this->epaisseur_lame(),
            ) ?? throw new \DomainException('Valeur forfaitaire ug non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie en W/m².K
     */
    public function uw(): float
    {
        return $this->get('uw', function (): float {
            return $this->uw_saisi() ?? $this->repository->uw(
                ug: $this->ug(),
                type_baie: $this->type_baie(),
                presence_soubassement: $this->presence_soubassement(),
                materiau: $this->materiau(),
                presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire uw non trouvée');
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
                ug: $rule->ug(),
                uw: $rule->uw(),
            ));
        }
    }
}
