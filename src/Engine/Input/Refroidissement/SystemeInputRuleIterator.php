<?php

namespace App\Engine\Input\Refroidissement;

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
        return $this->data()->refroidissement->systemes;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
