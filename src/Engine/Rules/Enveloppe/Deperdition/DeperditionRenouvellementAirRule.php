<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Exposition;
use App\Domain\Enveloppe\Paroi\Paroi;
use App\Domain\Enveloppe\Paroi\TypeParoi;
use App\Domain\Enveloppe\Permeabilite\Permeabilite;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRUle};
use App\Engine\Rules\Ventilation\PerformanceVentilationRule;
use App\Engine\Table\EnveloppeTableValeurRepository;

final class DeperditionRenouvellementAirRule extends Rule
{
    use WithBatiment, WithBatimentRUle;

    public function __construct(
        private EnveloppeTableValeurRepository $repository,
    ) {}

    // * Données d'entrée

    public function exposition(): Exposition
    {
        return $this->input()->enveloppe->exposition();
    }

    public function q4pa_conv_saisi(): ?float
    {
        return $this->input()->enveloppe->q4pa_conv();
    }

    // * Données intermédiaires

    public function hvent(): float
    {
        return $this->get('hvent', function (): float {
            return $this->require(PerformanceVentilationRule::class)->hvent();
        });
    }

    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function (): float {
            return $this->require(PerformanceVentilationRule::class)->qvarep_conv();
        });
    }

    public function qvasouf_conv(): float
    {
        return $this->get('qvasouf_conv', function (): float {
            return $this->require(PerformanceVentilationRule::class)->qvasouf_conv();
        });
    }

    public function smea_conv(): float
    {
        return $this->get('smea_conv', function (): float {
            return $this->require(PerformanceVentilationRule::class)->smea_conv();
        });
    }

    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return $this->input()->enveloppe->parois()
                ->filter(fn(Paroi $paroi) => $paroi->type_paroi() !== TypeParoi::PLANCHER_BAS)
                ->map(fn(Paroi $paroi) => $this->requireIterator(DeperditionParoiRule::class, $paroi)->sdep())
                ->reduce(fn($carry, $sdep) => $carry + $sdep);
        });
    }

    /**
     * Isolation majoritaire des murs et plafonds
     */
    public function isolation_murs_plafonds(): bool
    {
        return $this->get('isolation_murs_plafonds', function (): bool {
            $sdep = 0;
            $sdep_isole = 0;

            foreach ($this->input()->enveloppe->murs() as $item) {
                $rule = $this->requireIterator(DeperditionMurRule::class, $item);
                $sdep += $rule->sdep();
                $sdep_isole += $rule->isolation() ? $rule->sdep() : 0;
            }
            foreach ($this->input()->enveloppe->planchers_hauts() as $item) {
                $rule = $this->requireIterator(DeperditionPlancherHautRule::class, $item);
                $sdep += $rule->sdep();
                $sdep_isole += $rule->isolation() ? $rule->sdep() : 0;
            }
            return $sdep_isole > $sdep / 2;
        });
    }

    /**
     * Présence majoritaire de joints d'étanchéité aux menuiseries
     */
    public function presence_joints_menuiserie(): bool
    {
        return $this->get('presence_joints_menuiserie', function (): bool {
            $sdep = 0;
            $sdep_joints = 0;

            foreach ($this->input()->enveloppe->baies() as $item) {
                $rule = $this->requireIterator(DeperditionBaieRule::class, $item);
                $sdep += $rule->sdep();
                $sdep_joints += $item->menuiserie()->presence_joint ? $rule->sdep() : 0;
            }
            foreach ($this->input()->enveloppe->portes() as $item) {
                $rule = $this->requireIterator(DeperditionPorteRule::class, $item);
                $sdep += $rule->sdep();
                $sdep_joints += $item->menuiserie()->presence_joint ? $rule->sdep() : 0;
            }
            return $sdep_joints > $sdep / 2;
        });
    }

    // * Données calculées

    /**
     * Déperditions thermiques de l'enveloppe par renouvellement d'air exprimées en W/K
     */
    public function dr(): float
    {
        return $this->get('dr', function (): float {
            return $this->hvent() + $this->hperm();
        });
    }

    /**
     * Déperdition thermique par renouvellement d’air due au vent par degré d’écart entre
     * l’intérieur et l’extérieur exprimé en W/K
     */
    public function hperm(): float
    {
        return $this->get('hperm', function (): float {
            return 0.34 * $this->qvinf();
        });
    }

    /**
     * Débit d’air dû aux infiltrations liées au vent exprimé en m3/h
     */
    public function qvinf(): float
    {
        return $this->get('qvinf', function (): float {
            $volume_habitable = $this->volume_reference();
            $hauteur_sous_plafond = $this->hauteur_sous_plafond();
            $e = $this->e();
            $f = $this->f();
            $n50 = $this->n50();
            $qvasouf_conv = $this->qvasouf_conv();
            $qvarep_conv = $this->qvarep_conv();

            $qvinf = $volume_habitable * $n50 * $e;
            $qvinf /= 1 + ($f / $e) * \pow(($qvasouf_conv - $qvarep_conv) / ($hauteur_sous_plafond * $n50), 2);
            return $qvinf;
        });
    }

    /**
     * Valeur conventionnelle de la perméabilité sous 4Pa en m3/(h.m2)
     */
    public function q4pa_conv(): float
    {
        return $this->get('q4pa_conv', function () {
            return $this->q4pa_conv_saisi() ?? $this->repository->q4pa_conv(
                type_batiment: $this->type_batiment(),
                annee_construction: $this->annee_construction(),
                presence_joints_menuiserie: $this->presence_joints_menuiserie(),
                isolation_murs_plafonds: $this->isolation_murs_plafonds(),
            ) ?? throw new \DomainException('Valeur forfaitaire Q4PaConv non trouvée');
        });
    }

    /**
     * Renouvellement d'air sous 50 Pascals exprimé en h-1
     */
    public function n50(): float
    {
        return $this->q4pa() / (\pow(4 / 50, 2 / 3) * $this->volume_reference());
    }

    /**
     * Perméabilité sous 4 Pa de la zone exprimée en m3/h
     */
    public function q4pa(): float
    {
        return $this->q4pa_env() + 0.45 * $this->smea_conv() * $this->surface_reference();
    }

    /**
     * Perméabilité de l'enveloppe exprimée en m3/h
     */
    public function q4pa_env(): float
    {
        return $this->q4pa_conv() * $this->sdep();
    }

    /**
     * Coefficients de protection
     */
    public function e(): float
    {
        return $this->exposition()->e();
    }

    /**
     * Coefficients de protection
     */
    public function f(): float
    {
        return $this->exposition()->f();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
            permeabilite: Permeabilite::create(
                hvent: $this->hvent(),
                hperm: $this->hperm(),
                q4pa_conv: $this->q4pa_conv(),
                qvarep_conv: $this->qvarep_conv(),
                qvasouf_conv: $this->qvasouf_conv(),
                smea_conv: $this->smea_conv(),
            )
        ));
    }
}
