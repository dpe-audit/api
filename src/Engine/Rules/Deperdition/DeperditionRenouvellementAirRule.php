<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Paroi\TypeParoi;
use App\Domain\Enveloppe\Permeabilite\Permeabilite;
use App\Engine\Input\Ventilation\InstallationInput;
use App\Engine\Rule;
use App\Engine\Table\EnveloppeTableValeurRepository;

final class DeperditionRenouvellementAirRule extends Rule
{
    public function __construct(
        private EnveloppeTableValeurRepository $repository,
    ) {}

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
     * @use DeperditionSystemeVentilationRule
     */
    public function hvent(): float
    {
        return $this->get('hvent', function (): float {
            return array_sum(array_map(
                fn(InstallationInput $item) => $item->hvent(),
                $this->data()->ventilation->installations,
            ));
        });
    }

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        return $this->get('qvarep_conv', function (): float {
            return array_sum(array_map(
                fn(InstallationInput $item) => $item->qvarep_conv() * $item->rdim(),
                $this->data()->ventilation->installations,
            ));
        });
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        return $this->get('qvasouf_conv', function (): float {
            return array_sum(array_map(
                fn(InstallationInput $item) => $item->qvasouf_conv() * $item->rdim(),
                $this->data()->ventilation->installations,
            ));
        });
    }

    /**
     * @use DeperditionSystemeVentilationRule
     */
    public function smea_conv(): float
    {
        return $this->get('smea_conv', function (): float {
            return array_sum(array_map(
                fn(InstallationInput $item) => $item->smea_conv() * $item->rdim(),
                $this->data()->ventilation->installations,
            ));
        });
    }

    /**
     * Débit d’air dû aux infiltrations liées au vent exprimé en m3/h
     */
    public function qvinf(): float
    {
        return $this->get('qvinf', function (): float {
            $volume_habitable = $this->data()->batiment->volume_habitable();
            $hauteur_sous_plafond = $this->data()->batiment->hauteur_sous_plafond();
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
            return $this->data()->enveloppe->q4pa_conv() ?? $this->repository->q4pa_conv(
                type_batiment: $this->data()->batiment->type_batiment(),
                annee_construction: $this->data()->batiment->annee_construction(),
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
        return $this->q4pa() / (\pow(4 / 50, 2 / 3) * $this->data()->batiment->volume_habitable());
    }

    /**
     * Perméabilité sous 4 Pa de la zone exprimée en m3/h
     */
    public function q4pa(): float
    {
        return $this->q4pa_env() + 0.45 * $this->smea_conv() * $this->data()->batiment->surface_habitable();
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
        return $this->data()->enveloppe->exposition()->e();
    }

    /**
     * Coefficients de protection
     */
    public function f(): float
    {
        return $this->data()->enveloppe->exposition()->f();
    }

    /**
     * Surface déperditive hors planchers bas en m²
     */
    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            $sdep = 0;
            foreach ($this->data()->enveloppe->parois() as $paroi) {
                if ($paroi->type_paroi() !== TypeParoi::PLANCHER_BAS) {
                    $sdep += $paroi->sdep();
                }
            }
            return $sdep;
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

            foreach ($this->data()->enveloppe->murs as $item) {
                $sdep = $item->sdep();
                $sdep_isole += $item->isolation() ? $item->sdep() : 0;
            }
            foreach ($this->data()->enveloppe->planchers_hauts as $item) {
                $sdep = $item->sdep();
                $sdep_isole += $item->isolation() ? $item->sdep() : 0;
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

            foreach ($this->data()->enveloppe->baies as $item) {
                $sdep = $item->sdep();
                $sdep_joints += $item->presence_joint() ? $item->sdep() : 0;
            }
            foreach ($this->data()->enveloppe->portes as $item) {
                $sdep = $item->sdep();
                $sdep_joints += $item->presence_joint() ? $item->sdep() : 0;
            }
            return $sdep_joints > $sdep / 2;
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->enveloppe()->calcule($this->ressource()->enveloppe()->data()->with(
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
