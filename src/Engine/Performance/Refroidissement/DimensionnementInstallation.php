<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Refroidissement\Entity\Installation;
use App\Engine\Performance\Rule;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DimensionnementInstallation extends Rule
{
    /**
     * @var array{surface_totale: float, surface_couverte: float}
     */
    private array $input;

    /**
     * Ratio de dimensionnement de l'installation de refroidissement
     */
    public function rdim(): float
    {
        return $this->input['surface_totale']
            ? $this->input['surface_couverte'] / $this->input['surface_totale']
            : 0;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->refroidissement()->installations() as $installation) {
            $installation->calcule($installation->data()->with(
                ...$this->__invoke(static::prepare($installation))
            ));
        }
    }

    private static function prepare(Installation $entity): array
    {
        $input = [];
        $input['surface_totale'] = $entity->refroidissement()->installations()->surface();
        $input['surface_couverte'] = $entity->surface();
        return $input;
    }

    /**
     * @return array{rdim: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('surface_totale', 'surface_couverte');
        $resolver->setAllowedTypes('surface_totale', 'float|int');
        $resolver->setAllowedTypes('surface_couverte', 'float|int');

        $this->input = $resolver->resolve($input);
        return ['rdim' => $this->rdim()];
    }
}
