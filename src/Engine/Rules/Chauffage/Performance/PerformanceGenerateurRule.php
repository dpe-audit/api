<?php

namespace App\Engine\Rules\Chauffage\Performance;

use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Engine\Input\Chauffage\{GenerateurInput, GenerateurInputRuleIterator};
use App\Engine\Tables\ChauffageTableValeurRepository;

abstract class PerformanceGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        protected ChauffageTableValeurRepository $repository
    ) {}

    /**
     * Coefficient de performance énergétique
     */
    public function scop(): ?float
    {
        return $this->get("scop", function (): float {
            if ($this->item()->scop_saisi()) {
                return $this->item()->scop_saisi();
            }
            $scops = [];
            foreach ($this->item()->emetteurs() as $emetteur) {
                $scops[] = $this->repository->scop(
                    zone_climatique: $this->data()->batiment->zone_climatique(),
                    type_generateur: $this->type_generateur(),
                    annee_installation_generateur: $this->item()->annee_installation(),
                    type_emission: $emetteur->type_emission(),
                ) ?? throw new \DomainException("Valeurs forfaitaires SCOP non trouvées");
            }
            return max($scops);
        });
    }

    /**
     * Coefficient de performance énergétique
     */
    public function rpn(): ?float
    {
        return $this->get("rpn", function (): float {
            return $this->item()->rpn_saisi() ?? $this->repository->rpn(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation_generateur: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rpn non trouvée');
        });
    }

    /**
     * Rendement à charge intermédiaire
     */
    public function rpint(): ?float
    {
        return $this->get("rpint", function (): float {
            return $this->item()->rpint_saisi() ?? $this->repository->rpint(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation_generateur: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rpint non trouvée');
        });
    }

    /**
     * Pertes à l'arrêt exprimées en W
     */
    public function qp0(): ?float
    {
        return $this->get("qp0", function (): float {
            if ($this->item()->qp0_saisi()) {
                return $this->item()->qp0_saisi();
            }
            $e = $this->item()->presence_ventouse() ? 1.75 : 2.5;
            $f = $this->item()->presence_ventouse() ? -0.55 : -0.8;

            if (null === $qp0 = $this->repository->qp0(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation_generateur: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
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
            return $this->item()->pveilleuse_saisi() ?? $this->repository->pveilleuse(
                type_generateur: $this->type_generateur(),
                energie_generateur: $this->energie_generateur(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation_generateur: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
            ) ?? throw new \DomainException('Valeur forfaitaire Pveilleuse non trouvée');
        });
    }

    /**
     * Température de fonctionnement à 30% de charge
     */
    public function tfonc30(): ?float
    {
        return $this->get("tfonc30", function (): float {
            if ($this->item()->tfonc30_saisi()) {
                return $this->item()->tfonc30_saisi();
            }
            if (0 === count($this->item()->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
            }

            $tfonc30 = [];
            foreach ($this->item()->emetteurs() as $emetteur) {
                $tfonc30[] = $this->repository->tfonc30(
                    type_generateur: $this->type_generateur(),
                    mode_combustion: $this->item()->mode_combustion(),
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_generateur: $this->item()->annee_installation(),
                    annee_installation_emetteur: $emetteur->annee_installation(),
                ) ?? throw new \DomainException('Valeur forfaitaire Tfonc30 non trouvée');
            }
            return max($tfonc30);
        });
    }

    /**
     * Température de fonctionnement à 100% de charge
     */
    public function tfonc100(): ?float
    {
        return $this->get("tfonc100", function (): float {
            if ($this->item()->tfonc100_saisi()) {
                return $this->item()->tfonc100_saisi();
            }
            if (0 === count($this->item()->emetteurs())) {
                throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
            }
            $tfonc100 = [];
            foreach ($this->item()->emetteurs() as $emetteur) {
                $tfonc100[] = $this->repository->tfonc100(
                    temperature_distribution: $emetteur->temperature_distribution(),
                    annee_installation_emetteur: $emetteur->annee_installation(),
                ) ?? throw new \DomainException('Valeur forfaitaire Tfonc100 non trouvée');
            }
            return max($tfonc100);
        });
    }

    protected function type_generateur(): TypeGenerateur
    {
        return $this->item()->type();
    }

    protected function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->energie();
    }

    abstract public static function supports(GenerateurInput $item): bool;

    /** @inheritDoc */
    public function collection(): array
    {
        return array_filter(parent::collection(), [static::class, 'supports']);
    }
}
