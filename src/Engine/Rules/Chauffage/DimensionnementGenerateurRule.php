<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Generateur\{Generateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Ecs\DimensionnementGenerateurRule as DimensionnementGenerateurEcsRule;
use App\Engine\Rules\Enveloppe\WithDeperditionRule;
use App\Engine\Tables\ChauffageTableValeurRepository;

/**
 * @extends RuleIterator<Generateur>
 */
abstract class DimensionnementGenerateurRule extends RuleIterator
{
    use WithBatimentRule, WithDeperditionRule;

    public function __construct(
        protected readonly ChauffageTableValeurRepository $repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->type() ?? TypeGenerateur::CHAUDIERE;
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

    public function pn_saisi(): ?float
    {
        return $this->item()->signaletique()->pn;
    }

    // * Données intermédiaires

    /**
     * @return float[]
     */
    public function rdim_systemes(): array
    {
        return $this->input()->ecs->systemes()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(DimensionnementSystemeRule::class, $item)->rdim())
            ->values();
    }

    public function pecs(): float
    {
        if (null === $this->item()->position()->generateur_mixte_id) {
            return 0;
        }
        $entity = $this->input()->ecs->generateurs()->find($this->item()->position()->generateur_mixte_id);
        return $this->requireIterator(DimensionnementGenerateurEcsRule::class, $entity)->pecs();
    }

    // * Données de sortie

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum($this->rdim_systemes());
        });
    }

    /**
     * Puissance conventionnelle de chauffage exprimée en W
     */
    public function pch(): float
    {
        return $this->get('pch', function (): float {
            $pch = (1.2 * $this->gv() * (19 -  $this->tbase())) / (1000 * \pow(0.95, 3)) * 1000 * $this->rdim();
            return $this->generateur_collectif() ? $pch * (1 / $this->ratio_proratisation()) : $pch;
        });
    }

    /**
     * TODO
     */
    public function ratio_proratisation(): float
    {
        return 1;
    }

    /**
     * Puissance nominale conventionnelle exprimée en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function (): float {
            if ($this->pn_saisi()) {
                return $this->pn_saisi();
            }
            if (false === $this->type_generateur()->is_chaudiere()) {
                return $this->pch();
            }
            if (false === $this->type_generateur()->is_poele_bouilleur()) {
                return $this->pch();
            }
            if (null === $pn = $this->repository->pn(
                position_chaudiere: $this->position_chaudiere(),
                annee_installation_generateur: $this->annee_installation(),
                pdim: $this->pdim(),
            )) {
                throw new \DomainException('Valeur forfaitaire Pn non trouvée');
            }
            return $pn;
        });
    }

    /**
     * Puissance de dimensionnement du générateur exprimée en kW
     */
    public function pdim(): float
    {
        return $this->get('pdim', function (): float {
            return max($this->pecs(), $this->pch());
        });
    }
}
