<?php

namespace App\Domain\Enveloppe\DoubleFenetre\Vitrage;

enum NatureGazLame: string
{
    case AIR = 'air';
    case ARGON = 'argon';
    case KRYPTON = 'krypton';
}
