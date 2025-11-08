<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableElement, XMLTableDatabase};
use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\ModeCombustion;
use App\Domain\Ecs\Generateur\TypeGenerateur;
use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Engine\Table\EcsTableValeurRepository;
use App\Services\ExpressionResolver\ExpressionResolver;

final class XMLEcsTableValeurRepository implements EcsTableValeurRepository
{
    public function __construct(
        private readonly XMLTableDatabase $db,
        private ExpressionResolver $expression_resolver,
    ) {}

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $presence_ventouse,
        float $pn
    ): ?float {
        return $this->db->repository('ecs.paux')
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

    public function rd(
        bool $production_volume_habitable,
        bool $reseau_collectif,
        bool $alimentation_contigue,
        ?BouclageReseau $bouclage_reseau
    ): ?float {
        return $this->db->repository('ecs.rd')
            ->createQuery()
            ->and('production_volume_habitable', $production_volume_habitable)
            ->and('reseau_collectif', $reseau_collectif)
            ->and('alimentation_contigue', $alimentation_contigue)
            ->and('bouclage_reseau', $bouclage_reseau)
            ->getOne()
            ?->floatval('rd');
    }

    public function rg(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur): ?float
    {
        return $this->db->repository('ecs.rg')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->getOne()
            ?->floatval('rg');
    }

    public function cr(
        PositionChauffeEau $position_chauffe_eau,
        float $volume_stockage,
        ?LabelGenerateur $label_generateur
    ): ?float {
        return $this->db->repository('ecs.cr')
            ->createQuery()
            ->and('position_chauffe_eau', $position_chauffe_eau)
            ->and('label_generateur', $label_generateur)
            ->andCompareTo('volume_stockage', $volume_stockage)
            ->getOne()
            ?->floatval('cr');
    }

    public function cop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        int $annee_installation
    ): ?float {
        return $this->db->repository('ecs.cop')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('type_generateur', $type_generateur)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne()
            ?->floatval('cop');
    }

    public function fecs(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        Usage $usage_solaire,
        int $annee_installation,
    ): ?float {
        return $this->db->repository('ecs.fecs')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('type_batiment', $type_batiment)
            ->and('usage_solaire', $usage_solaire)
            ->andCompareTo('anciennete_installation', (int) date('Y') - $annee_installation)
            ->getOne()
            ?->to(fn(XMLTableElement $record) => $record->floatval('fecs'));
    }

    public function rpn(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation,
        float $pn
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('rpn');
                return $this->expression_resolver->evalue($expression, ['Pn' => $pn]);
            });
    }

    public function qp0(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation,
        float $pn,
        float $e,
        float $f
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne()
            ?->to(function (XMLTableElement $record) use ($pn, $e, $f) {
                $pn = $record->floatval('pn_max') ? max($record->floatval('pn_max'), $pn) : $pn;
                $expression = $record->strval('qp0');
                $variables = ['Pn' => $pn, 'E' => $e, 'F' => $f];
                return $this->expression_resolver->evalue($expression, $variables);
            });
    }

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        int $annee_installation
    ): ?float {
        return $this->db->repository('ecs.combustion')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('energie_generateur', $energie_generateur)
            ->and('mode_combustion', $mode_combustion)
            ->andCompareTo('annee_installation_generateur', $annee_installation)
            ->getOne()
            ?->floatval('pveilleuse');
    }
}
