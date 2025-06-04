<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Refroidissement\Entity\{Systeme, ReseauFroid};
use App\Domain\Refroidissement\Enum\EnergieGenerateur;
use App\Engine\Performance\Rule;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmissionRefroidissement extends Rule
{
    /**
     * @var array{
     *      contenu_co2_reseau_froid: float|null,
     *      energie_generateur: EnergieGenerateur,
     *      cfr_j: float,
     * }
     */
    private array $input;

    /**
     * Emissions de CO2 exprimées en kg
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges_j(): float
    {
        if (null !== $this->input['contenu_co2_reseau_froid']) {
            return $this->input['cfr_j'] * $this->input['contenu_co2_reseau_froid'];
        }
        return $this->input['cfr_j'] * match ($this->input['energie_generateur']) {
            EnergieGenerateur::ELECTRICITE => 0.064,
            EnergieGenerateur::GAZ_NATUREL => 0.227,
            EnergieGenerateur::GPL => 0.272,
            EnergieGenerateur::RESEAU_FROID => 0.385,
        };
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->refroidissement()->systemes()->count()) {
            $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $emissions = Emissions::create(
                usage: Usage::REFROIDISSEMENT,
                callback: fn(ScenarioUsage $scenario) => $this->__invoke(static::prepare(
                    systeme: $systeme,
                    scenario: $scenario,
                ))['eges_j'],
            );

            $systeme->calcule($systeme->data()->with(
                emissions: $emissions
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                emissions: $emissions
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                emissions: $emissions
            ));
            $systeme->refroidissement()->calcule($systeme->refroidissement()->data()->with(
                emissions: $emissions
            ));
            $entity->calcule($entity->data()->with(
                emissions: $emissions,
            ));
        }
    }

    /**
     * @see \App\Engine\Performance\Refroidissement\ConsommationRefroidissement::cfr_j()
     */
    private function prepare(Systeme $systeme, ScenarioUsage $scenario): array
    {
        $input = [];
        $input['cfr_j'] = $systeme->data()->consommations->get($scenario);
        $input['contenu_co2_reseau_froid'] = $systeme->generateur()->reseau_froid()?->contenu_co2()->decimal();
        return $input;
    }

    /**
     * @return array{eges_j: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['contenu_co2_reseau_froid', 'energie_generateur', 'cfr_j']);
        $resolver->setAllowedTypes('contenu_co2_reseau_froid', ['float', 'null']);
        $resolver->setAllowedTypes('energie_generateur', EnergieGenerateur::class);
        $resolver->setAllowedTypes('cfr_j', 'float');

        $this->input = $resolver->resolve($input);

        return ['eges_j' => $this->eges_j()];
    }

    public static function dependencies(): array
    {
        return [ConsommationRefroidissement::class];
    }
}
