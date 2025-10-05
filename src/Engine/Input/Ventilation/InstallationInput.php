<?php

namespace App\Engine\Input\Ventilation;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Ventilation\Installation\{Installation, TypeVentilation};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Deperdition\DeperditionSystemeVentilationRule;
use App\Engine\Rules\Ventilation\{ConsommationInstallationRule, DebitVentilationRule, DimensionnementInstallationRule};

final class InstallationInput extends Input
{
    private ?GenerateurInput $generateur = null;

    public function __construct(
        public readonly Engine $context,
        public readonly Installation $entity,
    ) {}

    public function generateur(): GenerateurInput
    {
        return $this->generateur ??= array_find(
            $this->context->data()->ventilation->generateurs,
            fn(GenerateurInput $item) => $item->entity->id()->equals($this->entity->generateur()->id())
        );
    }

    public function surface(): float
    {
        return $this->entity->surface();
    }

    public function surface_totale(): float
    {
        return $this->entity->ventilation()->installations()->surface();
    }

    public function type_installation(): TypeVentilation
    {
        return $this->entity->type();
    }

    // * Données calculées

    public function dimensionnement_rule(): DimensionnementInstallationRule
    {
        return $this->requireIterator(DimensionnementInstallationRule::class, $this);
    }

    public function debit_rule(): DebitVentilationRule
    {
        return $this->requireIterator(DebitVentilationRule::class, $this);
    }

    public function deperdition_rule(): DeperditionSystemeVentilationRule
    {
        return $this->requireIterator(DeperditionSystemeVentilationRule::class, $this);
    }

    public function consommation_rule(): ConsommationInstallationRule
    {
        return $this->require(ConsommationInstallationRule::class);
    }

    public function rdim(): float
    {
        return $this->dimensionnement_rule()->rdim();
    }

    public function qvarep_conv(): float
    {
        return $this->debit_rule()->qvarep_conv();
    }

    public function qvasouf_conv(): float
    {
        return $this->debit_rule()->qvasouf_conv();
    }

    public function smea_conv(): float
    {
        return $this->debit_rule()->smea_conv();
    }

    public function hvent(): float
    {
        return $this->deperdition_rule()->hvent();
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommation_rule()->consommations();
    }
}
