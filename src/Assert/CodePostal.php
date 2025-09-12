<?php

namespace App\Assert;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CodePostal extends Constraint
{
    public string $message = 'Le code postal "{{ code_postal }}" n\'existe pas';
    public string $mode = 'strict';

    public function __construct(?string $mode = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }
}
