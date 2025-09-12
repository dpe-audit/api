<?php

namespace App\Domain\Batiment;

enum PeriodeConstruction: string
{
    case AVANT_1948 = 'avant_1948';
    case ENTRE_1948_1974 = '1948_1974';
    case ENTRE_1975_1977 = '1975_1977';
    case ENTRE_1978_1982 = '1978_1982';
    case ENTRE_1983_1988 = '1983_1988';
    case ENTRE_1989_2000 = '1989_2000';
    case ENTRE_2001_2005 = '2001_2005';
    case ENTRE_2006_2012 = '2006_2012';
    case ENTRE_2013_2021 = '2013_2021';
    case APRES_2021 = 'apres_2021';

    public static function from_opendata(string $value): self
    {
        return match ($value) {
            'avant 1948' => self::AVANT_1948,
            '1948-1974' => self::ENTRE_1948_1974,
            '1975-1977' => self::ENTRE_1975_1977,
            '1978-1982' => self::ENTRE_1978_1982,
            '1983-1988' => self::ENTRE_1983_1988,
            '1989-2000' => self::ENTRE_1989_2000,
            '2001-2005' => self::ENTRE_2001_2005,
            '2006-2012' => self::ENTRE_2006_2012,
            '2013-2021' => self::ENTRE_2013_2021,
            'après 2021' => self::APRES_2021,
        };
    }

    public static function fromAnnee(int $value): self
    {
        return match (true) {
            $value < 1948 => self::AVANT_1948,
            $value <= 1974 => self::ENTRE_1948_1974,
            $value <= 1977 => self::ENTRE_1975_1977,
            $value <= 1982 => self::ENTRE_1978_1982,
            $value <= 1988 => self::ENTRE_1983_1988,
            $value <= 2000 => self::ENTRE_1989_2000,
            $value <= 2005 => self::ENTRE_2001_2005,
            $value <= 2012 => self::ENTRE_2006_2012,
            $value <= 2021 => self::ENTRE_2013_2021,
            $value > 2021 => self::APRES_2021,
        };
    }

    public function intval(): int
    {
        return match ($this) {
            self::AVANT_1948 => 1947,
            self::ENTRE_1948_1974 => 1966,
            self::ENTRE_1975_1977 => 1976,
            self::ENTRE_1978_1982 => 1980,
            self::ENTRE_1983_1988 => 1985,
            self::ENTRE_1989_2000 => 1995,
            self::ENTRE_2001_2005 => 2003,
            self::ENTRE_2006_2012 => 2009,
            self::ENTRE_2013_2021 => 2017,
            self::APRES_2021 => 2021,
        };
    }

    public function min(): ?int
    {
        return match ($this) {
            self::AVANT_1948 => null,
            self::ENTRE_1948_1974 => 1948,
            self::ENTRE_1975_1977 => 1975,
            self::ENTRE_1978_1982 => 1978,
            self::ENTRE_1983_1988 => 1983,
            self::ENTRE_1989_2000 => 1989,
            self::ENTRE_2001_2005 => 2001,
            self::ENTRE_2006_2012 => 2006,
            self::ENTRE_2013_2021 => 2013,
            self::APRES_2021 => 2022,
        };
    }

    public function max(): ?int
    {
        return match ($this) {
            self::AVANT_1948 => 1947,
            self::ENTRE_1948_1974 => 1974,
            self::ENTRE_1975_1977 => 1977,
            self::ENTRE_1978_1982 => 1982,
            self::ENTRE_1983_1988 => 1988,
            self::ENTRE_1989_2000 => 2000,
            self::ENTRE_2001_2005 => 2005,
            self::ENTRE_2006_2012 => 2012,
            self::ENTRE_2013_2021 => 2021,
            self::APRES_2021 => null,
        };
    }
}
