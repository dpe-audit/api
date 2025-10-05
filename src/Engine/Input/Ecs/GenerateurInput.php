<?php

namespace App\Engine\Input\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\{Generateur, EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Engine\{Engine, Input};
use App\Engine\Input\Chauffage\GenerateurInput as GenerateurMixte;
use App\Engine\Rules\Ecs\Dimensionnement\DimensionnementGenerateurRule;
use App\Engine\Rules\Ecs\Performance\PerformanceGenerateurRule;
use App\Engine\Rules\Ecs\Perte\PerteGenerateurRule;

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
            $this->context->data()->ecs->systemes,
            fn(SystemeInput $item) => $item->entity->generateur() === $this->entity
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

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->entity->position()->reseau_chaleur?->contenu_co2();
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->entity->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function position_chauff_eau(): PositionChauffeEau
    {
        return $this->entity->position()->position_chauff_eau ?? PositionChauffeEau::CHAUFFE_EAU_VERTICAL;
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
        return $this->entity->position()->generateur_mixte_id ? array_find(
            $this->context->data()->chauffage->generateurs,
            fn(GenerateurMixte $item) => $item->entity->id()->equals($this->entity->position()->generateur_mixte_id)
        ) : null;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->entity->position()->position_volume_chauffe;
    }

    public function volume_stockage(): float
    {
        return $this->entity->signaletique()->volume_stockage ?? 0;
    }

    public function presence_ventouse(): bool
    {
        return $this->entity->signaletique()->presence_ventouse ?? false;
    }

    public function label(): ?LabelGenerateur
    {
        return $this->entity->signaletique()->label;
    }

    public function cop_saisi(): ?float
    {
        return $this->entity->signaletique()->cop;
    }

    public function pn_saisi(): ?float
    {
        return $this->entity->signaletique()->pn;
    }

    public function rpn_saisi(): ?float
    {
        return $this->entity->signaletique()->rpn;
    }

    public function qp0_saisi(): ?float
    {
        return $this->entity->signaletique()->qp0;
    }

    public function pveilleuse_saisi(): float
    {
        return $this->entity->signaletique()->pveilleuse ?? 0;
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementGenerateurRule
    {
        return $this->require(DimensionnementGenerateurRule::class);
    }

    public function perte_rule(): PerteGenerateurRule
    {
        return $this->require(PerteGenerateurRule::class);
    }

    public function performance_rule(): PerformanceGenerateurRule
    {
        return $this->require(PerformanceGenerateurRule::class);
    }

    public function pecs(): float
    {
        return $this->dimensionnement_rule()->pecs();
    }

    public function pn(): float
    {
        return $this->dimensionnement_rule()->pn();
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function cop(): ?float
    {
        return $this->performance_rule()->cop();
    }

    public function rpn(): ?float
    {
        return $this->performance_rule()->rpn();
    }

    public function qp0(): ?float
    {
        return $this->performance_rule()->qp0();
    }

    public function pveilleuse(): ?float
    {
        return $this->performance_rule()->pveilleuse();
    }

    public function pertes_generation(?Mois $mois = null): float
    {
        return $mois
            ? $this->perte_rule()->pertes_generation_j($mois)
            : $this->perte_rule()->pertes_generation();
    }

    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        return $mois
            ? $this->perte_rule()->pertes_generation_recuperables_j($mois)
            : $this->perte_rule()->pertes_generation_recuperables();
    }

    public function pertes_stockage_integre(): float
    {
        return $this->perte_rule()->pertes_stockage_integre();
    }

    public function pertes_stockage_integre_recuperables(?Mois $mois = null): float
    {
        return $mois
            ? $this->perte_rule()->pertes_stockage_integre_recuperables_j($mois)
            : $this->perte_rule()->pertes_stockage_integre_recuperables();
    }
}
