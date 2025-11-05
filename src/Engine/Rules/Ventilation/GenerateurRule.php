<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\RuleIterator;

abstract class GenerateurRule extends RuleIterator
{
     /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->ventilation->generateurs;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}