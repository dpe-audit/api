<?php

namespace App\Engine\Input\Ecs;

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
        return $this->data()->ecs->systemes;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
