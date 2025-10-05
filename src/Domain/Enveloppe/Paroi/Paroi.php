<?php

namespace App\Domain\Enveloppe\Paroi;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;

abstract class Paroi
{
    abstract public function id(): Id;
    abstract public function enveloppe(): Enveloppe;
    abstract public function mitoyennete(): Mitoyennete;
    abstract public static function type_paroi(): TypeParoi;
}
