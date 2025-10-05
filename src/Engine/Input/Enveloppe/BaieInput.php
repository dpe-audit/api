<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Baie\{Baie, TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};
use App\Domain\Enveloppe\Masque\Masque;
use App\Domain\Enveloppe\Paroi\{Mitoyennete, TypeParoi};
use App\Engine\Engine;
use App\Engine\Rules\Apport\FacteurEnsoleillement\FacteurEnsoleillementBaieRule;
use App\Engine\Rules\Apport\FacteurSolaire\FacteurSolaireBaieRule;
use App\Engine\Rules\Apport\SurfaceSudEquivalente\SurfaceSudEquivalenteBaieRule;
use App\Engine\Rules\Deperdition\DeperditionBaieRule;

final class BaieInput extends ParoiInput
{
    public function __construct(
        public readonly Engine $context,
        public readonly Baie $entity,
    ) {}

    public function type_paroi(): TypeParoi
    {
        return $this->entity->type_paroi();
    }

    public function ug_saisi(): ?float
    {
        return $this->entity->ug();
    }

    public function uw_saisi(): ?float
    {
        return $this->entity->uw();
    }

    public function ujn_saisi(): ?float
    {
        return $this->entity->ujn();
    }

    public function sw_saisi(): ?float
    {
        return $this->entity->sw();
    }

    public function surface(): float
    {
        return $this->entity->position()->surface;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->entity->position()->mitoyennete;
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

    public function type_fermeture(): TypeFermeture
    {
        return $this->entity->type_fermeture();
    }

    public function isolation(): bool
    {
        return $this->type_vitrage()->isolation();
    }

    public function inclinaison(): float
    {
        return $this->entity->position()->inclinaison;
    }

    public function orientation(): Orientation
    {
        return Orientation::from_azimut($this->entity->position()->orientation);
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

    public function presence_joint(): bool
    {
        return $this->entity->menuiserie()?->presence_joint ?? false;
    }

    public function presence_protection_solaire(): bool
    {
        return $this->entity->presence_protection_solaire();
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

    public function local_non_chauffe(): ?LncInput
    {
        return array_find(
            $this->context->data()->enveloppe->locaux_non_chauffes,
            fn(LncInput $item) => $item->entity === $this->entity->position()->local_non_chauffe
        );
    }

    public function double_fenetre(): ?DoubleFenetreInput
    {
        return array_find(
            $this->context->data()->enveloppe->doubles_fenetres,
            fn(DoubleFenetreInput $item) => $item->entity === $this->entity->position()->double_fenetre
        );
    }

    /**
     * @return array<MasqueInput>
     */
    public function masques(): array
    {
        return array_filter(
            $this->context->data()->enveloppe->masques,
            fn(MasqueInput $item) => $this->entity->masques()->has(fn(Masque $entity) => $entity === $item->entity)
        );
    }

    public function sdep(): float
    {
        /** @var DeperditionBaieRule $rule */
        $rule = $this->requireIterator(DeperditionBaieRule::class, $this);
        return $rule->sdep();
    }

    public function b(): float
    {
        /** @var DeperditionBaieRule $rule */
        $rule = $this->requireIterator(DeperditionBaieRule::class, $this);
        return $rule->b();
    }

    public function dp(): float
    {
        /** @var DeperditionBaieRule $rule */
        $rule = $this->requireIterator(DeperditionBaieRule::class, $this);
        return $rule->dp();
    }

    public function fe(): float
    {
        /** @var FacteurEnsoleillementBaieRule $rule */
        $rule = $this->requireIterator(FacteurEnsoleillementBaieRule::class, $this);
        return $rule->fe();
    }

    public function sw(): float
    {
        /** @var FacteurSolaireBaieRule $rule */
        $rule = $this->requireIterator(FacteurSolaireBaieRule::class, $this);
        return $rule->sw();
    }

    public function sse(?Mois $mois = null): float
    {
        /** @var SurfaceSudEquivalenteBaieRule $rule */
        $rule = $this->requireIterator(SurfaceSudEquivalenteBaieRule::class, $this);
        return $mois ? $rule->sse_j($mois) : $rule->sse();
    }
}
