<?php

namespace App\Engine\Input\Enveloppe;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<PlancherBasInput>
 */
abstract class PlancherBasInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->enveloppe->planchers_bas;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
