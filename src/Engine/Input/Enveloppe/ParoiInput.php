<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Paroi\TypeParoi;
use App\Engine\Input;

abstract class ParoiInput extends Input
{
    abstract public function type_paroi(): TypeParoi;
    abstract public function sdep(): float;
    abstract public function dp(): float;
}
