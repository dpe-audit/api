<?php

namespace App\Dto\Eclairage;

use App\Domain\Eclairage\Eclairage;

final class EclairageDto
{
    public function __construct() {}

    public function to(): Eclairage
    {
        return Eclairage::create();
    }

    public function __normalize(): array
    {
        return [];
    }
}
