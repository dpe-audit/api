<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Engine\Performance\Rule;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConsommationRefroidissement extends Rule
{
    /**
     * @var array{
     *      bfr_j: float,
     *      eer: float,
     *      rdim_installation: float,
     *      rdim_systeme: float,
     * }
     */
    private array $input;

    /**
     * Consommation annuelle de refroidissement pour le mois j en kWh
     */
    public function cfr_j(): float
    {
        $rdim = $this->input['rdim_installation'] * $this->input['rdim_systeme'];
        return 0.9 * ($this->input['bfr_j'] / $this->input['eer']) * $rdim;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->refroidissement()->systemes()->count()) {
            $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $consommations = Consommations::create(
                usage: Usage::REFROIDISSEMENT,
                energie: $systeme->generateur()->energie()->to(),
                callback: fn(ScenarioUsage $scenario) => $this->__invoke(static::prepare(
                    systeme: $systeme,
                    scenario: $scenario,
                ))['cfr_j']
            );

            $systeme->calcule($systeme->data()->with(
                consommations: $consommations,
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                consommations: $consommations,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                consommations: $consommations,
            ));
            $systeme->refroidissement()->calcule($systeme->refroidissement()->data()->with(
                consommations: $consommations,
            ));
            $entity->calcule($entity->data()->with(
                consommations: $consommations,
            ));
        }
    }

    /**
     * @see \App\Engine\Performance\Refroidissement\BesoinRefroidissement::bfr_j()
     * @see \App\Engine\Performance\Refroidissement\DimensionnementInstallation::rdim()
     * @see \App\Engine\Performance\Refroidissement\DimensionnementSysteme::rdim()
     * @see \App\Engine\Performance\Refroidissement\PerformanceGenerateur::eer()
     */
    private static function prepare(Systeme $systeme, ScenarioUsage $scenario): array
    {
        $input = [];
        $input['rdim_installation'] = $systeme->installation()->data()->rdim;
        $input['rdim_systeme'] = $systeme->data()->rdim;
        $input['eer'] = $systeme->generateur()->data()->eer;
        $input['bfr_j'] = $systeme->refroidissement()->data()->besoins->get($scenario);
        return $input;
    }

    /**
     * @return array{cfr_j: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('rdim_installation', 'rdim_systeme', 'eer', 'bfr_j');
        $resolver->setAllowedTypes('rdim_installation', 'float');
        $resolver->setAllowedTypes('rdim_systeme', 'float');
        $resolver->setAllowedTypes('eer', 'float');
        $resolver->setAllowedTypes('bfr_j', 'float');

        $this->input = $resolver->resolve($input);

        return ['cfr_j' => $this->cfr_j()];
    }

    public static function dependencies(): array
    {
        return [
            BesoinRefroidissement::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
            PerformanceGenerateur::class,
        ];
    }
}
