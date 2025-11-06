<?php

namespace App\Engine\Rules;

use App\Engine\Input;

trait WithInput
{
    abstract public function input(): Input;
}
