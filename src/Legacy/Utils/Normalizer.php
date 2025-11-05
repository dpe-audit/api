<?php

namespace App\Legacy\Utils;

final class Normalizer
{
    public static function referenceval(?string $reference): ?string
    {
        if (empty($reference)) {
            return null;
        }
        $value = \trim($reference);
        $value = \strtolower($value);
        $value = \str_replace('generateur:', '', $value);
        $value = \str_replace('emetteur:', '', $value);
        $value = \str_replace('ets:', '', $value);
        $value = \preg_replace('/\s/', '', $value);
        return $value;
    }
}
