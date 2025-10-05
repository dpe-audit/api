<?php

namespace App\Database\Observatoire\Transformer;

final class Error
{
    public function __construct(
        public readonly string $path,
        public readonly string $message,
    ) {}
}
