<?php

namespace App\Engine\Rules;

use App\Engine\{RuleInterface, RuleIterator};

trait WithRule
{
    /**
     * @template U
     * @param class-string<U> $className
     * @return U
     */
    abstract public function require(string $className): RuleInterface;

    /**
     * @template U
     * @param class-string<U> $className
     * @return U
     */
    abstract public function requireIterator(string $className, mixed $item): RuleIterator;
}
