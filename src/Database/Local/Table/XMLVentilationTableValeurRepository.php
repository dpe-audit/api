<?php

namespace App\Database\Local\Table;

use App\Database\Local\{XMLTableDatabase, XMLTableElement};
use App\Domain\Ventilation\Installation\TypeVentilation;
use App\Domain\Ventilation\Generateur\TypeGenerateur;
use App\Domain\Ventilation\Generateur\TypeVmc;
use App\Engine\Table\VentilationTableValeurRepository;

final class XMLVentilationTableValeurRepository implements VentilationTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    private function fetch_pvent(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?XMLTableElement {
        return $this->db->repository('ventilation.pvent')
            ->createQuery()
            ->and('type_generateur', $type_generateur)
            ->and('type_vmc', $type_vmc)
            ->and('generateur_collectif', $generateur_collectif)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
    }

    private function fetch_debit(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?XMLTableElement {
        return $this->db->repository('ventilation.debit')
            ->createQuery()
            ->and('type_ventilation', $type_ventilation)
            ->and('type_generateur', $type_generateur)
            ->and('type_vmc', $type_vmc)
            ->and('presence_echangeur_thermique', $presence_echangeur_thermique)
            ->and('generateur_collectif', $generateur_collectif)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
    }

    public function ratio_utilisation(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('ratio_utilisation');
    }

    public function pvent_moy(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('pvent_moy');
    }

    public function pvent(
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('pvent');
    }

    public function qvarep_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('qvarep_conv');
    }

    public function qvasouf_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('qvasouf_conv');
    }

    public function smea_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('smea_conv');
    }
}
