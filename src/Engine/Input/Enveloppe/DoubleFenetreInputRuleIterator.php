<?php

namespace App\Engine\Input\Enveloppe;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<DoubleFenetreInput>
 */
abstract class DoubleFenetreInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->enveloppe->doubles_fenetres;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
