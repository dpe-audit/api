<?php

namespace App\Legacy\Model;

use App\Domain\Common\ValueObject\Id;

trait WithId
{
    private string $id;

    public function id(): string
    {
        return $this->id ??= (string) Id::create();
    }
}
