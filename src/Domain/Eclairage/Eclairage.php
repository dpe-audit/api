<?php

namespace App\Domain\Eclairage;

final class Eclairage
{
    private EclairageData $data;

    public function __construct()
    {
        $this->data = EclairageData::create();
    }

    public static function create(): self
    {
        return new self();
    }

    public function reinitialise(): void
    {
        $this->data = EclairageData::create();
    }

    public function calcule(EclairageData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function data(): EclairageData
    {
        return $this->data;
    }
}
