<?php

namespace App\Database\Observatoire\Model;

trait WithReferences
{
    use WithId;

    /**
     * @return string[]
     */
    abstract public function identifiers(): array;

    /**
     * @param string[] $needles
     */
    public function match(array $needles): bool
    {
        foreach ($needles as $needle) {
            $needle = self::normalizeIdentifier($needle);
            foreach ($this->identifiers() as $identifier) {
                $identifier = self::normalizeIdentifier($identifier);
                if ($identifier === $needle) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function normalizeIdentifier(string $identifier): string
    {
        $value = \trim($identifier);
        $value = \strtolower($value);
        $value = \str_replace('generateur:', '', $value);
        $value = \str_replace('emetteur:', '', $value);
        $value = \str_replace('ets:', '', $value);
        $value = \preg_replace('/\s/', '', $value);
        return $value;
    }
}
