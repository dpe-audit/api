<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, TemperatureDistribution, TypeEmission};
use App\Domain\Chauffage\Generateur\{TypeGenerateur, EnergieGenerateur, Generateur};
use App\Domain\Chauffage\Generateur\Signaletique\ModeCombustion;
use App\Domain\Common\Enum\Mois;
use App\Engine\Context;

abstract class PerformanceGenerateurRule extends DimensionnementGenerateurRule
{
    abstract public static function supports(Generateur $entity): bool;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->generateurs()
            ->filter(fn(Generateur $item) => static::supports($item))
            ->values();
    }

    // * Données d'entrée

    public function generateur_mixte(): bool
    {
        return $this->item()->position()->generateur_mixte_id !== null;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->type();
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->energie();
    }

    public function mode_combustion(): ModeCombustion
    {
        return $this->item()->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
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

    // * Données intermédiaires

    public function bch_hp(?Mois $mois): float
    {
        return $this->require(PerformanceChauffageRule::class)->bch_hp($mois);
    }

    // * Données de sortie

    /**
     * Coefficient de performance énergétique
     */
    public function scop(): ?float
    {
        return $this->get("scop", function (): float {
            if ($this->scop_saisi()) {
                return $this->scop_saisi();
            }
            $scops = [];
            foreach ($this->emetteurs() as $emetteur) {
                $scops[] = $this->repository->scop(
                    zone_climatique: $this->zone_climatique(),
                    type_generateur: $this->type_generateur(),
                    annee_installation_generateur: $this->annee_installation(),
                    type_emission: $emetteur['type_emission'],
                ) ?? throw new \DomainException("Valeurs forfaitaires SCOP non trouvées");
            }
            return max($scops);
        });
    }

    /**
     * Coefficient de performance énergétique en %
     */
    public function rpn(): ?float
    {
        return $this->get("rpn", function (): float {
            return $this->rpn_saisi() ?? $this->repository->rpn(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->mode_combustion(),
                annee_installation_generateur: $this->annee_installation(),
                pn: $this->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rpn non trouvée');
        });
    }

    /**
     * Rendement à charge intermédiaire en %
     */
    public function rpint(): ?float
    {
        return $this->get("rpint", function (): float {
            return $this->rpint_saisi() ?? $this->repository->rpint(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->mode_combustion(),
                annee_installation_generateur: $this->annee_installation(),
                pn: $this->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rpint non trouvée');
        });
    }

    /**
     * Pertes à l'arrêt exprimées en W
     */
    public function qp0(): ?float
    {
        return $this->get("qp0", function (): float {
            if ($this->qp0_saisi()) {
                return $this->qp0_saisi();
            }
            $e = $this->presence_ventouse() ? 1.75 : 2.5;
            $f = $this->presence_ventouse() ? -0.55 : -0.8;

            if (null === $qp0 = $this->repository->qp0(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->mode_combustion(),
                annee_installation_generateur: $this->annee_installation(),
                pn: $this->pn(),
                e: $e,
                f: $f,
            )) {
                throw new \DomainException('Valeur forfaitaire QP0 non trouvée');
            }
            return $qp0 * 1000;
        });
    }

    /**
     * Puissance de la veilleuse exprimée en W
     */
    public function pveilleuse(): ?float
    {
        return $this->get("pveilleuse", function (): float {
            return $this->pveilleuse_saisi() ?? $this->repository->pveilleuse(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->mode_combustion(),
                annee_installation_generateur: $this->annee_installation(),
                pn: $this->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Pveilleuse non trouvée');
        });
    }

    /**
     * Température de fonctionnement à 30% de charge en °C
     */
    public function tfonc30(): ?float
    {
        return $this->get("tfonc30", function (): float {
            if ($this->tfonc30_saisi()) {
                return $this->tfonc30_saisi();
            }
            if (0 === count($this->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
            }

            $tfonc30 = [];
            foreach ($this->emetteurs() as $emetteur) {
                $tfonc30[] = $this->repository->tfonc30(
                    type_generateur: $this->type_generateur(),
                    mode_combustion: $this->mode_combustion(),
                    temperature_distribution: $emetteur['temperature_distribution'],
                    annee_installation_generateur: $this->annee_installation(),
                    annee_installation_emetteur: $emetteur['annee_installation'],
                ) ?? throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
            }
            return max($tfonc30);
        });
    }

    /**
     * Température de fonctionnement à 100% de charge en °C
     */
    public function tfonc100(): ?float
    {
        return $this->get("tfonc100", function (): float {
            if ($this->tfonc100_saisi()) {
                return $this->tfonc100_saisi();
            }
            if (0 === count($this->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
            }
            $tfonc100 = [];
            foreach ($this->emetteurs() as $emetteur) {
                $tfonc100[] = $this->repository->tfonc100(
                    temperature_distribution: $emetteur['temperature_distribution'],
                    annee_installation_emetteur: $emetteur['annee_installation'],
                ) ?? throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
            }
            return max($tfonc100);
        });
    }

    /**
     * Pertes de génération en Wh
     */
    public function pertes_generation(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation::{$mois->value}" : 'pertes_generation';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_generation($item));
            }
            $nref = $this->nref($mois);
            $cper = $this->presence_ventouse() ? 0.75 : 0.5;
            $qp0 = $this->qp0();
            $bch_hp = $this->bch_hp($mois);
            $pn = $this->pn();
            $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn));

            if ($this->generateur_mixte()) {
                $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn) + $nref * (1790 / 8760));
            }
            return $cper * $qp0 * $dper * $this->rdim();
        });
    }

    /**
     * Pertes de génération de chauffage récupérables en Wh
     */
    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation_recuperables::{$mois->value}" : 'pertes_generation_recuperables';
        return $this->get($key, function () use ($mois): float {
            return 0.48 * $this->pertes_generation($mois);
        });
    }

    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                pdim: $rule->pdim(),
                pn: $rule->pn(),
                scop: $rule->scop(),
                rpn: $rule->rpn(),
                rpint: $rule->rpint(),
                qp0: $rule->qp0(),
                pveilleuse: $rule->pveilleuse(),
                tfonc30: $rule->tfonc30(),
                tfonc100: $rule->tfonc100(),
            ));
        }
    }
}
