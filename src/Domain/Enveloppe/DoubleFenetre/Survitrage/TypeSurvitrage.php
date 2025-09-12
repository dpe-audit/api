<?php

namespace App\Domain\Enveloppe\DoubleFenetre\Survitrage;

enum TypeSurvitrage: string
{
    case SURVITRAGE_SIMPLE = 'survitrage_simple';
    case SURVITRAGE_FE = 'survitrage_fe';
}
