<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Engine\Performance\Rule;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DimensionnementSysteme extends Rule
{
    /**
     * @var array{systemes: int}
     */
    private array $input;

    /**
     * Ratio de dimensionnement du système de refroidissement
     */
    public function rdim(): float
    {
        return 1 / $this->input['systemes'];
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $systeme->calcule($systeme->data()->with(
                ...$this->__invoke(static::prepare($systeme))
            ));
        }
    }

    private static function prepare(Systeme $entity): array
    {
        $input = [];
        $input['systemes'] = $entity->refroidissement()->systemes()
            ->with_installation($entity->installation()->id())
            ->count();

        return $input;
    }

    /**
     * @return array{rdim: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('systemes');
        $resolver->setAllowedTypes('systemes', 'int');

        $this->input = $resolver->resolve($input);
        return ['rdim' => $this->rdim()];
    }
}
