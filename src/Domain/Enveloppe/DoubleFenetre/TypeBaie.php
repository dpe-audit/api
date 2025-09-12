<?php

namespace App\Domain\Enveloppe\DoubleFenetre;

enum TypeBaie: string
{
    case BRIQUE_VERRE_PLEINE = 'brique_verre_pleine';
    case BRIQUE_VERRE_CREUSE = 'brique_verre_creuse';
    case POLYCARBONATE = 'polycarbonate';
    case FENETRE_BATTANTE = 'fenetre_battante';
    case FENETRE_COULISSANTE = 'fenetre_coulissante';
    case PORTE_FENETRE_COULISSANTE = 'porte_fenetre_coulissante';
    case PORTE_FENETRE_BATTANTE = 'porte_fenetre_battante';

    public function is_paroi_vitree(): bool
    {
        return \in_array($this, [self::BRIQUE_VERRE_PLEINE, self::BRIQUE_VERRE_CREUSE, self::POLYCARBONATE]);
    }

    public function is_fenetre(): bool
    {
        return \in_array($this, [self::FENETRE_BATTANTE, self::FENETRE_COULISSANTE]);
    }

    public function is_porte_fenetre(): bool
    {
        return \in_array($this, [self::PORTE_FENETRE_COULISSANTE, self::PORTE_FENETRE_BATTANTE]);
    }

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [self::BRIQUE_VERRE_PLEINE, self::BRIQUE_VERRE_CREUSE]);
    }
}
