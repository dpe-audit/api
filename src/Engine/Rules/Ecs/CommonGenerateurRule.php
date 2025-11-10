<?php

namespace App\Engine\Rules\Ecs;

use App\Engine\Rules\Chauffage\DimensionnementGenerateurRule as DimensionnementGenerateurChauffageRule;
use App\Domain\Ecs\Generateur\{EnergieGenerateur, Generateur, TypeGenerateur};
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Generateur>
 */
abstract class CommonGenerateurRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ecs->generateurs()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function type(): TypeGenerateur
    {
        return $this->item()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->item()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->item()->position()->reseau_chaleur?->contenu_co2();
    }

    public function position_chauff_eau(): PositionChauffeEau
    {
        return $this->item()->position()->position_chauff_eau ?? PositionChauffeEau::CHAUFFE_EAU_VERTICAL;
    }

    public function annee_installation(): int
    {
        return $this->item()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function generateur_multi_batiment(): bool
    {
        return $this->item()->position()->generateur_multi_batiment;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->item()->position()->position_volume_chauffe;
    }

    public function label(): ?LabelGenerateur
    {
        return $this->item()->signaletique()->label;
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->item()->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function presence_ventouse(): bool
    {
        return $this->item()->signaletique()->presence_ventouse ?? false;
    }

    public function generateur_mixte(): bool
    {
        return $this->item()->position()->generateur_mixte_id !== null;
    }

    public function pn_saisi(): ?float
    {
        return $this->item()->signaletique()->pn;
    }

    public function cop_saisi(): ?float
    {
        return $this->item()->signaletique()->cop;
    }

    public function rpn_saisi(): ?float
    {
        return $this->item()->signaletique()->rpn;
    }

    public function qp0_saisi(): ?float
    {
        return $this->item()->signaletique()->qp0;
    }

    public function pveilleuse_saisi(): float
    {
        return $this->item()->signaletique()->pveilleuse ?? 0;
    }

    public function volume_stockage(): float
    {
        return $this->volume_stockage_integre() + $this->volume_stockage_independant();
    }

    public function volume_stockage_integre(): float
    {
        return $this->item()->signaletique()->volume_stockage;
    }

    public function volume_stockage_independant(): float
    {
        return $this->input()->ecs->systemes()->with_generateur($this->item()->id())->volume_stockage();
    }

    /**
     * @return float[]
     */
    public function rdim_systemes(): array
    {
        return $this->input()->ecs->systemes()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->rdim())
            ->values();
    }

    public function pch(): float
    {
        if (false === $this->generateur_mixte()) {
            return 0;
        }
        $entity = $this->input()->chauffage->generateurs()->find($this->item()->position()->generateur_mixte_id);
        return $this->requireIterator(DimensionnementGenerateurChauffageRule::class, $entity)->pch();
    }
}
