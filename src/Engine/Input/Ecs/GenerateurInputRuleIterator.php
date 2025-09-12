<?php

namespace App\Engine\Input\Ecs;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<GenerateurInput>
 */
abstract class GenerateurInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->ecs->generateurs;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
