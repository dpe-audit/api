<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Service\RefroidissementTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PerformanceGenerateur extends Rule
{
    /**
     * @var array{
     *    zone_climatique: ZoneClimatique,
     *    eer: float|null,
     *    annee_installation_generateur: Annee|null,
     *    annee_construction: Annee,
     * }
     */
    private array $input;

    public function __construct(private readonly RefroidissementTableValeurRepository $table_repository) {}

    /**
     * Année d'installation de référence du générateur
     */
    public function annee_installation(): Annee
    {
        return $this->input['annee_installation_generateur'] ?? $this->input['annee_construction'];
    }

    /**
     * Coefficient d'efficience énergétique
     */
    public function eer(): float
    {
        return $this->get('eer', function () {
            if ($this->input['eer']) {
                return $this->input['eer'];
            }
            if (null === $eer = $this->table_repository->eer(
                zone_climatique: $this->input['zone_climatique'],
                annee_installation_generateur: $this->annee_installation(),
            )) {
                throw new \DomainException('Valeur forfaitaire EER non trouvé');
            }
            return $eer;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->refroidissement()->generateurs() as $generateur) {
            $generateur->calcule($generateur->data()->with(
                ...$this->__invoke(static::prepare(audit: $entity, generateur: $generateur)),
            ));
        }
    }

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function prepare(Audit $audit, Generateur $generateur): array
    {
        $input = [];
        $input['zone_climatique'] = $audit->data()->zone_climatique;
        $input['annee_installation_generateur'] = $generateur->annee_installation();
        $input['annee_construction'] = $audit->batiment()->annee_construction;
        $input['eer'] = $generateur->seer();
        return $input;
    }

    /**
     * @return array{eer: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['zone_climatique', 'eer', 'annee_installation_generateur', 'annee_construction']);
        $resolver->setAllowedTypes('zone_climatique', ZoneClimatique::class);
        $resolver->setAllowedTypes('eer', ['float', 'null']);
        $resolver->setAllowedTypes('annee_installation_generateur', [Annee::class, 'null']);
        $resolver->setAllowedTypes('annee_construction', Annee::class);

        $this->input = $resolver->resolve($input);
        $this->clear();

        return ['eer' => $this->eer()];
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
