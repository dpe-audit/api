<?php

namespace App\Utils;

final class Math
{
    public static function interpolation_lineaire(float $x, float $x1, float $x2, float $y1, float $y2): float
    {
        return $y1 + ($x - $x1) * (($y2 - $y1) / \max($x2 - $x1, 1));
    }

    public static function moyenne_ponderee(array $valeurs, array $coefficients): float
    {
        $somme = 0;
        $somme_coefficients = 0;
        foreach ($valeurs as $i => $valeur) {
            $somme += $valeur * $coefficients[$i];
            $somme_coefficients += $coefficients[$i];
        }
        return $somme_coefficients ? $somme / $somme_coefficients : 0;
    }
}
