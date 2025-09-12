<?php

namespace App\Domain\Chauffage\Installation\Regulation;

enum TypeIntermittence: string
{
    case ABSENT = 'absent';
    case CENTRAL = 'central';
    case CENTRAL_MINIMUM_TEMPERATURE = 'central_minimum_temperature';
    case TERMINAL_DETECTION_PRESENCE = 'terminal_detection_presence';
    case TERMINAL_MINIMUM_TEMPERATURE = 'terminal_minimum_temperature';
    case TERMINAL_MINIMUM_TEMPERATURE_DETECTION_PRESENCE = 'terminal_minimum_temperature_detection_presence';

    public static function determine(
        Regulation $regulation_centrale,
        Regulation $regulation_terminale,
        bool $chauffage_collectif,
    ): self {
        if ($chauffage_collectif) {
            return match (true) {
                $regulation_terminale->detection_presence => self::TERMINAL_DETECTION_PRESENCE,
                $regulation_centrale->minimum_temperature => self::CENTRAL_MINIMUM_TEMPERATURE,
                default => self::ABSENT,
            };
        }
        return match (true) {
            $regulation_terminale->minimum_temperature && $regulation_terminale->detection_presence => self::TERMINAL_MINIMUM_TEMPERATURE_DETECTION_PRESENCE,
            $regulation_terminale->minimum_temperature => self::TERMINAL_MINIMUM_TEMPERATURE,
            $regulation_centrale->minimum_temperature => self::CENTRAL_MINIMUM_TEMPERATURE,
            $regulation_centrale->presence_regulation => self::CENTRAL,
            default => self::ABSENT,
        };
    }
}
