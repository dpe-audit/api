<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\ValueObject\Id;

trait WithId
{
    private Id $id;

    public function id(): Id
    {
        return $this->id ??= Id::create();
    }
}
