<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, TypeEmetteur, TypeEmission, TemperatureDistribution};
use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Chauffage\Installation\Regulation\TypeIntermittence;
use App\Domain\Chauffage\Systeme\{Systeme, Configuration};
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};
use App\Engine\Rules\Enveloppe\{WithDeperditionRule, WithInertieRule};

/**
 * @extends RuleIterator<Systeme>
 */
abstract class CommonSystemeRule extends RuleIterator
{
    use WithBatiment, WithBatimentRule, WithDeperditionRule, WithInertieRule;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->systemes()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function surface_installation(): float
    {
        return $this->item()->installation()->surface();
    }

    public function installation_collective(): bool
    {
        return $this->item()->installation()->systemes()->has_generateur_collectif();
    }

    public function niveaux_desservis(): ?int
    {
        return $this->item()->reseau()?->niveaux_desservis;
    }

    public function type_distribution(): ?TypeDistribution
    {
        return $this->item()->reseau()?->type_distribution;
    }

    public function presence_fluide_frigorigene(): ?bool
    {
        return $this->item()->reseau()?->presence_fluide_frigorigene;
    }

    public function isolation_reseau(): IsolationReseau
    {
        return $this->item()->reseau()?->isolation ?? IsolationReseau::NON_ISOLE;
    }

    public function comptage_individuel(): bool
    {
        return $this->item()->installation()->comptage_individuel();
    }

    public function regulation(): bool
    {
        return $this->regulation_centrale() || $this->regulation_terminale();
    }

    public function regulation_centrale(): bool
    {
        return $this->item()->installation()->regulation_centrale()->presence_regulation;
    }

    public function regulation_terminale(): bool
    {
        return $this->item()->installation()->regulation_terminale()->presence_regulation;
    }

    public function type_intermittence(): TypeIntermittence
    {
        return $this->get('type_intermittence', function (): TypeIntermittence {
            return TypeIntermittence::determine(
                regulation_centrale: $this->item()->installation()->regulation_centrale(),
                regulation_terminale: $this->item()->installation()->regulation_terminale(),
                chauffage_collectif: $this->systeme_collectif(),
            );
        });
    }

    /**
     * @return TemperatureDistribution[]
     */
    public function temperatures_distribution(): array
    {
        $values = $this->item()->emetteurs()->map(fn(Emetteur $entity) => $entity->temperature_distribution())->values();
        return array_unique($values);
    }

    public function type_systeme(): TypeChauffage
    {
        return $this->item()->type();
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->generateur()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->generateur()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function bienergie_generateur(): ?EnergieGenerateur
    {
        return $this->item()->generateur()->bienergie();
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->item()->generateur()->position()->reseau_chaleur?->contenu_co2();
    }

    public function annee_installation_generateur(): int
    {
        return $this->item()->generateur()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function systeme_collectif(): bool
    {
        return $this->item()->generateur()->position()->generateur_collectif;
    }

    public function generateur_multi_batiment(): bool
    {
        return $this->item()->generateur()->position()->generateur_multi_batiment;
    }

    public function presence_ventouse(): bool
    {
        return $this->item()->generateur()->signaletique()->presence_ventouse ?? false;
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->item()->generateur()->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
    }

    public function pn_saisi(): ?float
    {
        return $this->item()->generateur()->signaletique()->pn;
    }

    public function scop_saisi(): ?float
    {
        return $this->item()->generateur()->signaletique()->scop;
    }

    public function cascade(): ?int
    {
        return null !== $this->item()->cascade() ? min($this->item()->cascade(), 2) : null;
    }

    /**
     * @return array<int, array{
     *      type: ?TypeEmetteur,
     *      type_emission: TypeEmission,
     *      temperature_distribution: ?TemperatureDistribution,
     *      robinet_thermostatique: ?bool
     * }>
     */
    public function emetteurs(): array
    {
        $values = $this->item()->emetteurs()
            ->map(fn(Emetteur $entity) => [
                'type' => $entity->type(),
                'type_emission' => $entity->type_emission(),
                'temperature_distribution' => $entity->temperature_distribution(),
                'robinet_thermostatique' => $entity->presence_robinet_thermostatique(),
            ])
            ->values();

        if (0 === count($values)) {
            $values[] = [
                'type' => null,
                'type_emission' => TypeEmission::from_type_generateur($this->item()->generateur()->type()),
                'temperature_distribution' => null,
                'robinet_thermostatique' => null,
            ];
        }
        return $values;
    }

    public function bch(): float
    {
        return $this->require(PerformanceChauffageRule::class)->bch();
    }

    public function fch(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->fch();
    }

    public function pn(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pn();
    }

    public function scop(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->scop();
    }

    public function rpn(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->rpn();
    }

    public function rpint(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->rpint();
    }

    public function qp0(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->qp0() / 1000;
    }

    public function pveilleuse(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pveilleuse() / 1000;
    }

    public function tfonc30(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->tfonc30();
    }

    public function tfonc100(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->tfonc100();
    }

    public function rdim(): float
    {
        return $this->requireIterator(DimensionnementSystemeRule::class, $this->item())->rdim();
    }

    public function configuration(): Configuration
    {
        return $this->requireIterator(DimensionnementSystemeRule::class, $this->item())->configuration();
    }
}
