<?php

namespace App\Domain\Enveloppe;

enum Exposition: string
{
    case EXPOSITION_SIMPLE = 'exposition_simple';
    case EXPOSITION_MULTIPLE = 'exposition_multiple';

    public function e(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 0.02,
            self::EXPOSITION_MULTIPLE => 0.07,
        };
    }

    public function f(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 20,
            self::EXPOSITION_MULTIPLE => 15,
        };
    }
}
