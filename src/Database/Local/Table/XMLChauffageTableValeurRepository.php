<?php

namespace App\Database\Local\Table;

use App\Services\ExpressionResolver\ExpressionResolver;
use App\Database\Local\{XMLTableElement, XMLTableDatabase};
use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Chauffage\Emetteur\{TemperatureDistribution, TypeEmission};
use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Chauffage\Installation\Regulation\TypeIntermittence;
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Engine\Tables\ChauffageTableValeurRepository;

final class XMLChauffageTableValeurRepository implements ChauffageTableValeurRepository
{
    public function __construct(
        private readonly XMLTableDatabase $db,
        private ExpressionResolver $expression_resolver,
    ) {}

    public function i0(
        TypeBatiment $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        bool $comptage_individuel
    ): ?float {
        return $this->db->repository('chauffage.i0')
            ->createQuery()
            ->and('type_batiment', $type_batiment)
            ->and('type_emission', $type_emission)
            ->and('type_intermittence', $type_intermittence)
            ->and('chauffage_central', $chauffage_central)
            ->and('regulation_terminale', $regulation_terminale)
            ->and('chauffage_collectif', $chauffage_collectif)
            ->and('inertie_lourde', $inertie_lourde)
            ->and('comptage_individuel', $comptage_individuel)
            ->getOne()
            ?->floatval('i0');
    }

    public function fch(ZoneClimatique $zone_climatique, TypeBatiment $type_batiment): ?float
    {
        return $this->db->repository('chauffage.fch')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('type_batiment', $type_batiment)
            ->getOne()
            ?->to(fn(XMLTableElement $record) => $record->floatval('fch'));
    }

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $generateur_multi_batiment,
        bool $presence_ventouse,
        float $pn
    ): ?float {
        return $this->db->repository('chauffage.paux')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('presence_ventouse', $presence_ventouse)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ?? $pn;
                $expression = $record->strval('paux');
                return $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
            });
    }

    public function pn(
        PositionChaudiere $position_chaudiere,
        int $annee_installation_generateur,
        float $pdim
    ): ?float {
        return $this->db->repository('chauffage.pn')
            ->createQuery()
            ->and('position_chaudiere', $position_chaudiere)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->andCompareTo('pdim', $pdim)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pdim) {
                $expression = $record->strval('pn');
                return $this->expression_resolver->evalue($expression, ['Pdim' => $pdim]);
            });
    }

    public function rd(
        TypeDistribution $type_distribution,
        TemperatureDistribution $temperature_distribution,
        bool $reseau_collectif,
        ?IsolationReseau $isolation_reseau
    ): ?float {
        return $this->db->repository('chauffage.rd')
            ->createQuery()
            ->and('type_distribution', $type_distribution)
            ->and('temperature_distribution', $temperature_distribution)
            ->and('reseau_collectif', $reseau_collectif)
            ->and('isolation_reseau', $isolation_reseau)
            ->getOne()
            ?->floatval('rd');
    }

    public function re(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur
    ): ?float {
        return $this->db->repository('chauffage.re')
            ->createQuery()
            ->and('type_emission', $type_emission)
            ->and('type_generateur', $type_generateur)
            ->and('label_generateur', $label_generateur)
            ->getOne()
            ?->floatval('re');
    }

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?LabelGenerateur $label_generateur,
        int $annee_installation_generateur
    ): ?float {
        return $this->db->repository('chauffage.rg')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('label_generateur', $label_generateur)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->floatval('rg');
    }

    public function rr(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
        bool $reseau_collectif,
        bool $presence_regulation_terminale,
        ?bool $presence_robinet_thermostatique,
    ): ?float {
        return $this->db->repository('chauffage.rr')
            ->createQuery()
            ->and('type_emission', $type_emission)
            ->and('type_generateur', $type_generateur)
            ->and('label_generateur', $label_generateur)
            ->and('reseau_collectif', $reseau_collectif)
            ->and('presence_robinet_thermostatique', $presence_robinet_thermostatique)
            ->and('presence_regulation_terminale', $presence_regulation_terminale)
            ->getOne()
            ?->floatval('rr');
    }

    public function scop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        int $annee_installation_generateur
    ): ?float {
        return $this->db->repository('chauffage.scop')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('type_generateur', $type_generateur)
            ->and('type_emission', $type_emission)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->floatval('scop');
    }

    public function rpn(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn
    ): ?float {
        return $this->db->repository('chauffage.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('rpn');
                return $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
            });
    }

    public function rpint(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn
    ): ?float {
        return $this->db->repository('chauffage.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('rpint');
                return $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
            });
    }

    public function qp0(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
        float $e,
        float $f,
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn, $e, $f) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('qp0');
                $value =  $this->expression_resolver->evalue($expression, ['Pn' => $pn, 'E' => $e, 'F' => $f]);
                return $value;
            });
    }

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn
    ): ?float {
        return $this->db->repository('chauffage.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->and('energie_generateur', $energie_generateur)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->floatval('pveilleuse');
    }

    public function tfonc30(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
        int $annee_installation_generateur
    ): ?float {
        return $this->db->repository('chauffage.tfonc30')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->and('temperature_distribution', $temperature_distribution)
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne()
            ?->floatval('tfonc30');
    }

    public function tfonc100(
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur
    ): ?float {
        return $this->db->repository('chauffage.tfonc100')
            ->createQuery()
            ->and('temperature_distribution', $temperature_distribution)
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur)
            ->getOne()
            ?->floatval('tfonc100');
    }
}
