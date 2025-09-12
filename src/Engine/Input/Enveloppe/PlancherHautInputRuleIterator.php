<?php

namespace App\Engine\Input\Enveloppe;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<PlancherHautInput>
 */
abstract class PlancherHautInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->enveloppe->planchers_hauts;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
