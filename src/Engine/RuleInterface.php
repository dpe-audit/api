<?php

namespace App\Engine;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.engine.rule')]
interface RuleInterface
{
    public function setContext(Context $context): void;

    public function __invoke(mixed $data, Context $context): void;
}
