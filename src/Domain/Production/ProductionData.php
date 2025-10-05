<?php

namespace App\Domain\Production;

use Webmozart\Assert\Assert;

final class ProductionData
{
    public function __construct(
        public readonly ?float $ppv,
    ) {}

    public static function create(?float $ppv = null): self
    {
        Assert::nullOrGreaterThanEq($ppv, 0);
        return new self(ppv: $ppv);
    }

    public function with(?float $ppv = null): self
    {
        return self::create(
            ppv: $ppv ?? $this->ppv,
        );
    }
}
