<?php

namespace App\Engine\Input\Enveloppe;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<MasqueInput>
 */
abstract class MasqueInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->enveloppe->masques;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
