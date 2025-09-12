<?php

namespace App\Dto\Chauffage\Installation;

use App\Domain\Chauffage\Installation\Regulation\Regulation;

final class RegulationDto
{
    public function __construct(
        public bool $presence_regulation,
        public ?bool $minimum_temperature,
        public ?bool $detection_presence,
    ) {}

    public static function from(Regulation $data): self
    {
        return new self(
            presence_regulation: $data->presence_regulation,
            minimum_temperature: $data->minimum_temperature,
            detection_presence: $data->detection_presence,
        );
    }

    public function __normalize(): array
    {
        return [
            'presence_regulation' => $this->presence_regulation,
            'minimum_temperature' => $this->minimum_temperature,
            'detection_presence' => $this->detection_presence,
        ];
    }
}
