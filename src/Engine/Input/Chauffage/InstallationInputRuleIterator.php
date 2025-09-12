<?php

namespace App\Engine\Input\Chauffage;

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
        return $this->data()->chauffage->installations;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
