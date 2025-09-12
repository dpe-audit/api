<?php

namespace App\Engine\Input\Chauffage;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<SystemeInput>
 */
abstract class SystemeInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->chauffage->systemes;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
