<?php

namespace App\Engine\Rules\Chauffage\Rendement;

use App\Domain\Chauffage\Emetteur\{TypeEmission, TemperatureDistribution};
use App\Engine\Input\Chauffage\{EmetteurInput, SystemeInput, SystemeInputRuleIterator};
use App\Engine\Tables\ChauffageTableValeurRepository;

abstract class RendementSystemeRule extends SystemeInputRuleIterator
{
    public function __construct(
        protected ChauffageTableValeurRepository $repository
    ) {}

    /**
     * Inverse du rendement du système
     */
    public function ich(): float
    {
        return $this->get('ich', function (): float {
            $rd = $this->rd();
            $re = $this->re();
            $rg = $this->rg();
            $rr = $this->rr();
            return 1 / ($rd * $re * $rg * $rr);
        });
    }

    /**
     * Rendement de génération
     */
    abstract public function rg(): float;

    /**
     * Rendement de distribution
     */
    public function rd(): float
    {
        return $this->get('rd', function (): float {
            $values = [];
            foreach ($this->temperatures_distribution() as $temperature_distribution) {
                $values[] = $this->repository->rd(
                    type_distribution: $this->item()->type_distribution(),
                    temperature_distribution: $temperature_distribution,
                    isolation_reseau: $this->item()->isolation_reseau(),
                    reseau_collectif: $this->item()->generateur()->generateur_collectif(),
                ) ?? throw new \DomainException('Valeur forfaitaire Rd non trouvée');
            }
            return count($values) ? array_sum($values) / count($values) : 1;
        });
    }

    /**
     * Rendement d'émission
     */
    public function re(): float
    {
        return $this->get('re', function (): float {
            $values = [];
            foreach ($this->emissions() as $emission) {
                $values[] = $this->repository->re(
                    type_emission: $emission,
                    type_generateur: $this->item()->generateur()->type(),
                    label_generateur: $this->item()->generateur()->label(),
                ) ?? throw new \DomainException('Valeur forfaitaire Re non trouvée');
            }
            return array_sum($values) / count($values);
        });
    }

    /**
     * Rendement de régulation
     */
    public function rr(): float
    {
        return $this->get('rr', function (): float {
            if (count($this->item()->emetteurs())) {
                $values = [];
                foreach ($this->item()->emetteurs() as $emetteur) {
                    $values[] = $this->repository->rr(
                        type_emission: $emetteur->type_emission(),
                        type_generateur: $this->item()->generateur()->type(),
                        label_generateur: $this->item()->generateur()->label(),
                        reseau_collectif: $this->item()->generateur()->generateur_collectif(),
                        presence_regulation_terminale: $this->item()->installation()->regulation_terminale()->presence_regulation,
                        presence_robinet_thermostatique: $emetteur->presence_robinet_thermostatique(),
                    ) ?? throw new \DomainException('Valeur forfaitaire Rr non trouvée');
                }
                return array_sum($values) / count($values);
            }
            if (null === $value = $this->repository->rr(
                type_emission: TypeEmission::from_type_generateur($this->item()->generateur()->type()),
                type_generateur: $this->item()->generateur()->type(),
                label_generateur: $this->item()->generateur()->label(),
                reseau_collectif: $this->item()->generateur()->generateur_collectif(),
                presence_regulation_terminale: $this->item()->installation()->regulation_terminale()->presence_regulation,
                presence_robinet_thermostatique: null,
            )) {
                throw new \DomainException('Valeur forfaitaire Rr non trouvée');
            }
            return $value;
        });
    }

    /**
     * @return TypeEmission[]
     */
    private function emissions(): array
    {
        $emissions = array_map(fn(EmetteurInput $item) => $item->type_emission(), $this->item()->emetteurs());
        $emissions[] = TypeEmission::from_type_generateur($this->item()->generateur()->type());
        return array_unique($emissions);
    }

    /**
     * @return TemperatureDistribution[]
     */
    private function temperatures_distribution(): array
    {
        $values = array_map(fn(EmetteurInput $item) => $item->temperature_distribution(), $this->item()->emetteurs());
        return array_unique($values);
    }

    abstract public static function supports(SystemeInput $item): bool;

    /** @inheritDoc */
    public function collection(): array
    {
        return array_filter(parent::collection(), [static::class, 'supports']);
    }
}
