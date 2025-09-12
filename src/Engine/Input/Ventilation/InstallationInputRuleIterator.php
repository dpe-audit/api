<?php

namespace App\Engine\Input\Ventilation;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<InstallationInput>
 */
abstract class InstallationInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->ventilation->installations;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
