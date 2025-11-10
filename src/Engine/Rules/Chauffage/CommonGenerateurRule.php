<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, TemperatureDistribution, TypeEmission};
use App\Domain\Chauffage\Generateur\{Generateur, EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Enveloppe\WithDeperditionRule;
use App\Engine\RuleIterator;
use App\Engine\Rules\Ecs\DimensionnementGenerateurRule as DimensionnementGenerateurEcsRule;

/**
 * @extends RuleIterator<Generateur>
 */
abstract class CommonGenerateurRule extends RuleIterator
{
    use WithBatimentRule, WithDeperditionRule;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->generateurs()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function generateur_mixte(): bool
    {
        return $this->item()->position()->generateur_mixte_id !== null;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function bienergie_generateur(): ?EnergieGenerateur
    {
        return $this->item()->bienergie();
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->item()->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function position_chaudiere(): PositionChaudiere
    {
        return $this->item()->position()->position_chaudiere ?? PositionChaudiere::CHAUDIERE_SOL;
    }

    public function annee_installation(): int
    {
        return $this->item()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function generateur_collectif(): bool
    {
        return $this->item()->position()->generateur_collectif;
    }

    public function generateur_multi_batiment(): bool
    {
        return $this->item()->position()->generateur_multi_batiment;
    }

    public function pn_saisi(): ?float
    {
        return $this->item()->signaletique()->pn;
    }

    public function presence_ventouse(): bool
    {
        return $this->item()->signaletique()->presence_ventouse ?? false;
    }

    public function presence_regulation_combustion(): bool
    {
        return $this->item()->signaletique()->presence_regulation_combustion ?? false;
    }

    public function scop_saisi(): ?float
    {
        return $this->item()->signaletique()->scop;
    }

    public function qp0_saisi(): ?float
    {
        return $this->item()->signaletique()->qp0;
    }

    public function rpn_saisi(): ?float
    {
        return $this->item()->signaletique()->rpn;
    }

    public function rpint_saisi(): ?float
    {
        return $this->item()->signaletique()->rpint;
    }

    public function pveilleuse_saisi(): ?float
    {
        return $this->item()->signaletique()->pveilleuse;
    }

    public function tfonc30_saisi(): ?float
    {
        return $this->item()->signaletique()->tfonc30;
    }

    public function tfonc100_saisi(): ?float
    {
        return $this->item()->signaletique()->tfonc100;
    }

    /**
     * @return array<int, array{
     *      type_emission: TypeEmission,
     *      temperature_distribution: TemperatureDistribution,
     *      annee_installation: int,
     * }>
     */
    public function emetteurs(): array
    {
        return $this->item()->emetteurs()
            ->map(fn(Emetteur $entity) => [
                'type_emission' => $entity->type_emission(),
                'temperature_distribution' => $entity->temperature_distribution(),
                'annee_installation' => $entity->annee_installation() ?? $this->input()->batiment->annee_construction,
            ])->values();
    }

    public function bch_hp(Scenario $scenario, ?Mois $mois): float
    {
        return $this->require(PerformanceChauffageRule::class)->bch_hp($scenario, $mois);
    }

    /**
     * @return float[]
     */
    public function rdim_systemes(): array
    {
        return $this->input()->chauffage->systemes()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(DimensionnementSystemeRule::class, $item)->rdim())
            ->values();
    }

    public function pecs(): float
    {
        if (false === $this->generateur_mixte()) {
            return 0;
        }
        $entity = $this->input()->ecs->generateurs()->find($this->item()->position()->generateur_mixte_id);
        return $this->requireIterator(DimensionnementGenerateurEcsRule::class, $entity)->pecs();
    }
}
