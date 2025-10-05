<?php

namespace App\Database\Observatoire\Transformer;

use App\Database\Observatoire\Model\XMLRessource;

/**
 * @property array<Error> $errors
 * @property array<string, mixed> $options
 */
final class Context
{
    public function __construct(
        private XMLRessource $ressource,
        private array $errors = [],
        private array $options = [],
    ) {}

    public function ressource(): XMLRessource
    {
        return $this->ressource;
    }

    /**
     * @return array<Error>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function options(): array
    {
        return $this->options;
    }
}
