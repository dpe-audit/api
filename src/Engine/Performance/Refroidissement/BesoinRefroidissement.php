<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Besoins;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Engine\Performance\Apport\ApportEnveloppe;
use App\Engine\Performance\Deperdition\DeperditionEnveloppe;
use App\Engine\Performance\Inertie\InertieEnveloppe;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\{ScenarioClimatique, ZoneThermique};
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BesoinRefroidissement extends Rule
{
    /**
     * @var array{
     *      mois: Mois,
     *      scenario: ScenarioUsage,
     *      inertie: Inertie,
     *      gv: float,
     *      sh: float,
     *      as_fr_j: float,
     *      ai_fr_j: float,
     *      text_fr_j: float|null,
     *      nref_fr_j: float|null,
     * }
     */
    private array $input;

    /**
     * Besoin mensuel de refroidissement exprimé en kWh
     */
    public function bfr_j(): float
    {
        return $this->get($this->getCacheKey('bfr_j'), function () {
            $text_fr_j = $this->input['text_fr_j'];
            $nref_fr_j = $this->input['nref_fr_j'];

            if (!$text_fr_j || !$nref_fr_j) {
                return 0;
            }
            if (0.5 > ($rbth = $this->rbth())) {
                return 0;
            }
            $fut = $this->fut();
            $tint = $this->tint();

            if (0.5 > $rbth) {
                return 0;
            }
            $bfr = ($this->input['ai_fr_j'] + $this->input['as_fr_j']) / 1000;
            $bfr -= $fut * ($this->input['gv'] / 1000) * ($tint - $text_fr_j) * $nref_fr_j;
            return max($bfr, 0);
        });
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth(): float
    {
        return $this->get($this->getCacheKey('rbth'), function () {
            $tint = $this->tint();
            $gv = $this->input['gv'];
            $ai_fr_j = $this->input['ai_fr_j'];
            $as_fr_j = $this->input['as_fr_j'];
            $text_fr_j = $this->input['text_fr_j'];
            $nref_fr_j = $this->input['nref_fr_j'];

            $rbth = $gv * ($text_fr_j - $tint) * $nref_fr_j;
            return $rbth ? ($ai_fr_j + $as_fr_j) / $rbth : 0;
        });
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut(): float
    {
        return $this->get($this->getCacheKey('fut'), function () {
            $t = $this->t();
            $rbth = $this->rbth();
            $a = 1 + ($t / 15);

            return $rbth > 0 && $rbth !== 1
                ?  (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1))
                : $a / ($a + 1);
        });
    }

    /**
     * Température de consigne en froid exprimée en °C
     */
    public function tint(): float
    {
        return $this->get($this->getCacheKey('tint'), function () {
            return match ($this->input['scenario']) {
                ScenarioUsage::CONVENTIONNEL => 26,
                ScenarioUsage::DEPENSIER => 28,
            };
        });
    }

    /**
     * Constante de temps de la zone pour le refroidissement exprimée en J/K
     */
    public function t(): float
    {
        return $this->get('t', function () {
            return $this->cin() / (3600 * $this->input['gv']);
        });
    }

    /**
     * Capacité thermique intérieure efficace de la zone exprimée en J/K
     */
    public function cin(): float
    {
        return $this->get('cin', function () {
            return $this->input['inertie']->cin() * $this->input['sh'];
        });
    }

    public function apply(Audit $entity): void
    {
        $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
            besoins: Besoins::create(
                usage: Usage::REFROIDISSEMENT,
                callback: function (ScenarioUsage $scenario, Mois $mois) use ($entity) {
                    $output = $this->__invoke(static::prepare(
                        entity: $entity,
                        scenario: $scenario,
                        mois: $mois
                    ))['bfr_j'];
                    return $output['bfr_j'];
                }
            ),
        ));
    }

    /**
     * @see \App\Engine\Performance\Scenario\ZoneThermique::surface_habitable()
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     * @see \App\Engine\Performance\Deperdition\DeperditionEnveloppe::gv()
     * @see \App\Engine\Performance\Apport\ApportEnveloppe::apports_internes()
     * @see \App\Engine\Performance\Apport\ApportEnveloppe::apports_solaires()
     * @see \App\Engine\Performance\Inertie\InertieEnveloppe::inertie()
     */
    private static function prepare(Audit $entity, ScenarioUsage $scenario, Mois $mois): array
    {
        $input = [];
        $input['mois'] = $mois;
        $input['scenario'] = $scenario;
        $input['sh'] = $entity->data()->surface_habitable;
        $input['gv'] = $entity->enveloppe()->data()->deperditions->get();
        $input['inertie'] = $entity->enveloppe()->data()->inertie;
        $input['ai_fr_j'] = $entity->enveloppe()->data()->apports->apports_internes_fr(scenario: $scenario, mois: $mois,);
        $input['as_fr_j'] = $entity->enveloppe()->data()->apports->apports_solaires_fr(scenario: $scenario, mois: $mois);
        $input['text_fr_j'] = $entity->data()->sollicitations_exterieures->text_fr(scenario: $scenario, mois: $mois);
        $input['nref_fr_j'] = $entity->data()->sollicitations_exterieures->nref_fr(scenario: $scenario, mois: $mois);
        return $input;
    }

    private function getCacheKey(string $name): string
    {
        return "{$this->input['scenario']}_{$this->input['mois']}_{$name}";
    }

    /**
     * @return array{bfr_j: float, rbth: float, fut: float, tint: float, t: float, cin: float}
     */
    public function __invoke(array $input): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['mois', 'scenario', 'sh', 'gv', 'inertie', 'ai_fr_j', 'as_fr_j', 'text_fr_j', 'nref_fr_j']);
        $resolver->setAllowedTypes('mois', Mois::class);
        $resolver->setAllowedTypes('scenario', ScenarioUsage::class);
        $resolver->setAllowedTypes('sh', 'float');
        $resolver->setAllowedTypes('gv', 'float');
        $resolver->setAllowedTypes('inertie', Inertie::class);
        $resolver->setAllowedTypes('ai_fr_j', 'float');
        $resolver->setAllowedTypes('as_fr_j', 'float');
        $resolver->setAllowedTypes('text_fr_j', ['float', 'null']);
        $resolver->setAllowedTypes('nref_fr_j', ['float', 'null']);

        $this->input = $resolver->resolve($input);

        return [
            'bfr_j' => $this->bfr_j(),
            'rbth' => $this->rbth(),
            'fut' => $this->fut(),
            'tint' => $this->tint(),
            't' => $this->t(),
            'cin' => $this->cin(),
        ];
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            ApportEnveloppe::class,
            InertieEnveloppe::class,
        ];
    }
}
