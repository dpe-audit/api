<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Generateur\{EnergieGenerateur, Generateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Ecs\GenerateurInput as GenerateurMixte;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Chauffage\Dimensionnement\DimensionnementGenerateurRule;
use App\Engine\Rules\Chauffage\Performance\PerformanceGenerateurRule;
use App\Engine\Rules\Chauffage\Perte\PerteGenerationRule;

final class GenerateurInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Generateur $entity,
    ) {}

    /**
     * @return SystemeInput[]
     */
    public function systemes(): array
    {
        return array_filter(
            $this->context->data()->chauffage->systemes,
            fn(SystemeInput $item) => $item->entity->generateur() === $this->entity
        );
    }

    /**
     * @return EmetteurInput[]
     */
    public function emetteurs(): array
    {
        return array_filter(
            $this->context->data()->chauffage->emetteurs,
            fn(EmetteurInput $item) => null !== $this->entity->emetteurs()->find($item->entity->id())
        );
    }

    public function type(): TypeGenerateur
    {
        return $this->entity->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->entity->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function bienergie(): ?EnergieGenerateur
    {
        return $this->entity->bienergie();
    }

    public function pac_hybride(): bool
    {
        return $this->type()->is_pac() && null !== $this->bienergie();
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->entity->position()->reseau_chaleur?->contenu_co2();
    }

    public function position_chaudiere(): PositionChaudiere
    {
        return $this->entity->position()->position_chaudiere ?? PositionChaudiere::CHAUDIERE_SOL;
    }

    public function annee_installation(): int
    {
        return $this->entity->annee_installation()
            ?? $this->context->data()->batiment->annee_construction();
    }

    public function generateur_multi_batiment(): bool
    {
        return $this->entity->position()->generateur_multi_batiment;
    }

    public function generateur_collectif(): bool
    {
        return $this->entity->position()->generateur_collectif;
    }

    public function generateur_mixte(): ?GenerateurMixte
    {
        return $this->entity->position()->generateur_mixte;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->entity->position()->position_volume_chauffe;
    }

    public function cascade(): ?int
    {
        return $this->entity->position()->cascade;
    }

    public function priorite_cascade(): ?int
    {
        return min($this->entity->position()->priorite_cascade, 2);
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->entity->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function label(): ?LabelGenerateur
    {
        return $this->entity->signaletique()->label;
    }

    public function presence_ventouse(): bool
    {
        return $this->entity->signaletique()->presence_ventouse ?? false;
    }

    public function presence_regulation_combustion(): bool
    {
        return $this->entity->signaletique()->presence_regulation_combustion ?? false;
    }

    public function pn_saisi(): ?float
    {
        return $this->entity->signaletique()->pn;
    }

    public function scop_saisi(): ?float
    {
        return $this->entity->signaletique()->scop;
    }

    public function pveilleuse_saisi(): ?float
    {
        return $this->entity->signaletique()->pveilleuse;
    }

    public function qp0_saisi(): ?float
    {
        return $this->entity->signaletique()->qp0;
    }

    public function rpn_saisi(): ?float
    {
        return $this->entity->signaletique()->rpn;
    }

    public function rpint_saisi(): ?float
    {
        return $this->entity->signaletique()->rpint;
    }

    public function tfonc30_saisi(): ?float
    {
        return $this->entity->signaletique()->tfonc30;
    }

    public function tfonc100_saisi(): ?float
    {
        return $this->entity->signaletique()->tfonc100;
    }

    // * Données calculées

    private function performance_rule(): PerformanceGenerateurRule
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this);
    }

    private function dimensionnement_rule(): DimensionnementGenerateurRule
    {
        return $this->requireIterator(DimensionnementGenerateurRule::class, $this);
    }

    private function perte_generation_rule(): PerteGenerationRule
    {
        return $this->requireIterator(PerteGenerationRule::class, $this);
    }

    public function pch(): float
    {
        return $this->dimensionnement_rule()->pch();
    }

    public function pn(): float
    {
        return $this->dimensionnement_rule()->pn();
    }

    public function pdim(): float
    {
        return $this->dimensionnement_rule()->pdim();
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function scop(): ?float
    {
        return $this->performance_rule()->scop();
    }

    public function rpn(): ?float
    {
        return $this->performance_rule()->rpn();
    }

    public function rpint(): ?float
    {
        return $this->performance_rule()->rpint();
    }

    public function qp0(): ?float
    {
        return $this->performance_rule()->qp0();
    }

    public function pveilleuse(): ?float
    {
        return $this->performance_rule()->pveilleuse();
    }

    public function tfonc30(): ?float
    {
        return $this->performance_rule()->tfonc30();
    }

    public function tfonc100(): ?float
    {
        return $this->performance_rule()->tfonc100();
    }

    public function pertes_generation(?Mois $mois = null): float
    {
        $rule = $this->perte_generation_rule();
        return $mois ? $rule->pertes_generation_j($mois) : $rule->pertes_generation();
    }

    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        $rule = $this->perte_generation_rule();
        return $mois ? $rule->pertes_generation_recuperables_j($mois) : $rule->pertes_generation_recuperables();
    }
}
