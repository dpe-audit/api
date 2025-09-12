<?php

namespace App\Domain\Enveloppe;

enum Inertie: string
{
    case TRES_LOURDE = 'tres_lourde';
    case LOURDE = 'lourde';
    case MOYENNE = 'moyenne';
    case LEGERE = 'legere';

    public static function from_enum_classe_inertie_id(int $id): self
    {
        return match ($id) {
            1 => self::TRES_LOURDE,
            2 => self::LOURDE,
            3 => self::MOYENNE,
            4 => self::LEGERE,
        };
    }

    public function lourde(): bool
    {
        return $this->value === self::TRES_LOURDE || $this->value === self::LOURDE;
    }

    /**
     * @deprecated Use lourde() instead
     */
    public function est_lourd(): bool
    {
        return $this->value === self::TRES_LOURDE || $this->value === self::LOURDE;
    }

    /**
     * Exposant utilisé pour le calcul des apports gratuits
     */
    public function exposant(): float
    {
        return match ($this) {
            self::TRES_LOURDE, self::LOURDE => 3.6,
            self::MOYENNE => 2.9,
            self::LEGERE => 2.5,
        };
    }

    /**
     * Cin - Capacité thermique intérieure efficace de la zone (J/K)
     */
    public function cin(): float
    {
        return match ($this) {
            self::TRES_LOURDE, self::LOURDE => 260000,
            self::MOYENNE => 165000,
            self::LEGERE => 110000,
        };
    }
}
