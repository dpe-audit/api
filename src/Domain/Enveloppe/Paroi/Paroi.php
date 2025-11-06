<?php

namespace App\Domain\Enveloppe\Paroi;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Lnc\Lnc;

abstract class Paroi
{
    abstract public function id(): Id;
    abstract public function enveloppe(): Enveloppe;
    abstract public function local_non_chauffe(): ?Lnc;
    abstract public function mitoyennete(): Mitoyennete;
    abstract public function surface(): float;
    abstract public static function type_paroi(): TypeParoi;
}
