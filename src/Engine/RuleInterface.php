<?php

namespace App\Engine;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.engine.rule')]
interface RuleInterface
{
    public function __invoke(Engine $context): void;
}
