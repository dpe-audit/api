<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\ValueObject\Regulation as Value;

final class Regulation
{
    public function __construct(
        public bool $presence_regulation,

        public bool $minimum_temperature,

        public bool $detection_presence,
    ) {}

    public static function from(Value $value): self
    {
        return new self(
            presence_regulation: $value->presence_regulation,
            minimum_temperature: $value->minimum_temperature,
            detection_presence: $value->detection_presence,
        );
    }
}
