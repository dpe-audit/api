<?php

namespace App\Domain\Batiment;

enum ZoneClimatique: string
{
    const CODE_DEPARTEMENT_H1a = "02|14|27|28|59|60|61|62|75|76|77|78|80|91|92|93|94|95";
    const CODE_DEPARTEMENT_H1b = "08|10|45|51|52|54|55|57|58|67|68|70|88|89|90";
    const CODE_DEPARTEMENT_H1c = "01|03|05|15|19|21|23|25|38|39|42|43|63|69|71|73|74|87";
    const CODE_DEPARTEMENT_H2a = "22|29|35|50|56";
    const CODE_DEPARTEMENT_H2b = "16|17|18|36|37|41|44|49|53|72|79|85|86";
    const CODE_DEPARTEMENT_H2c = "09|12|24|31|32|33|40|46|47|64|65|81|82";
    const CODE_DEPARTEMENT_H2d = "04|07|26|48|84";
    const CODE_DEPARTEMENT_H3 = "06|11|13|30|34|66|83|2A|2B|20";

    case H1a = 'H1a';
    case H1b = 'H1b';
    case H1c = 'H1c';
    case H2a = 'H2a';
    case H2b = 'H2b';
    case H2c = 'H2c';
    case H2d = 'H2d';
    case H3 = 'H3';

    public static function from_code_departement(string $code_departement): self
    {
        return match (true) {
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H1a)) => self::H1a,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H1b)) => self::H1b,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H1c)) => self::H1c,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H2a)) => self::H2a,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H2b)) => self::H2b,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H2c)) => self::H2c,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H2d)) => self::H2d,
            \in_array($code_departement, \explode("|", self::CODE_DEPARTEMENT_H3)) => self::H3,
            default => null,
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::H1a, self::H1b, self::H1c => 'H1',
            self::H2a, self::H2b, self::H2c, self::H2d => 'H2',
            self::H3 => 'H3',
        };
    }

    public function h1(): bool
    {
        return \substr($this->value, 2) === "H1";
    }

    public function h2(): bool
    {
        return \substr($this->value, 2) === "H2";
    }

    public function h3(): bool
    {
        return \substr($this->value, 2) === "H3";
    }

    /**
     * Taux de couverture de la partie PAC
     */
    public function taux_couverture_pac(): float
    {
        return match ($this) {
            self::H1a, self::H1b, self::H1c => 0.8,
            self::H2a, self::H2b, self::H2c, self::H2d => 0.83,
            self::H3 => 0.88,
        };
    }
}
