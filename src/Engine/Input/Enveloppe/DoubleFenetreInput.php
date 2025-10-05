<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetre;
use App\Domain\Enveloppe\DoubleFenetre\Menuiserie\Materiau;
use App\Domain\Enveloppe\DoubleFenetre\Position\TypePose;
use App\Domain\Enveloppe\DoubleFenetre\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\DoubleFenetre\TypeBaie;
use App\Domain\Enveloppe\DoubleFenetre\Vitrage\{NatureGazLame, TypeVitrage};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Apport\FacteurSolaire\FacteurSolaireDoubleFenetreRule;
use App\Engine\Rules\Deperdition\DeperditionDoubleFenetreRule;

final class DoubleFenetreInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly DoubleFenetre $entity,
    ) {}

    public function ug_saisi(): ?float
    {
        return $this->entity->ug();
    }

    public function uw_saisi(): ?float
    {
        return $this->entity->uw();
    }

    public function sw_saisi(): ?float
    {
        return $this->entity->sw();
    }

    public function type_baie(): TypeBaie
    {
        return $this->entity->type();
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->entity->vitrage()->type;
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        if (null === $this->entity->survitrage()) {
            return null;
        }
        return $this->entity->survitrage()->type ?? TypeSurvitrage::SURVITRAGE_SIMPLE;
    }

    public function inclinaison(): float
    {
        return $this->entity->position()->inclinaison;
    }

    public function type_pose(): TypePose
    {
        return $this->entity->position()->type_pose ?? TypePose::NU_EXTERIEUR;
    }

    public function presence_soubassement(): bool
    {
        return $this->entity->position()->presence_soubassement ?? false;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->entity->menuiserie()?->presence_rupteur_pont_thermique ?? false;
    }

    public function materiau(): Materiau
    {
        return $this->entity->menuiserie()?->materiau ?? Materiau::PVC;
    }

    public function epaisseur_lame(): float
    {
        if ($this->entity->vitrage()->epaisseur_lame) {
            return $this->entity->vitrage()->epaisseur_lame;
        }
        if ($this->entity->vitrage()->type === TypeVitrage::SIMPLE_VITRAGE) {
            return $this->entity->survitrage() !== null
                ? $this->entity->survitrage()->epaisseur_lame ?? 6
                : 0;
        }
        return $this->entity->vitrage()->type->vitrage_complexe() ? 6 : 0;
    }

    public function nature_lame(): ?NatureGazLame
    {
        return $this->entity->vitrage()->nature_lame
            ?? $this->entity->vitrage()->type->vitrage_complexe() ? NatureGazLame::AIR : null;
    }

    public function ug(): float
    {
        /** @var DeperditionDoubleFenetreRule $rule */
        $rule = $this->requireIterator(DeperditionDoubleFenetreRule::class, $this);
        return $rule->ug();
    }

    public function uw(): float
    {
        /** @var DeperditionDoubleFenetreRule $rule */
        $rule = $this->requireIterator(DeperditionDoubleFenetreRule::class, $this);
        return $rule->uw();
    }

    public function sw(): float
    {
        /** @var FacteurSolaireDoubleFenetreRule $rule */
        $rule = $this->requireIterator(FacteurSolaireDoubleFenetreRule::class, $this);
        return $rule->sw();
    }
}
