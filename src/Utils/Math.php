<?php

namespace App\Utils;

final class Math
{
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
