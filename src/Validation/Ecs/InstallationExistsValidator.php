<?php

namespace App\Validation\Ecs;

use App\Dto\Ecs\EcsDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class InstallationExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof InstallationExists) {
            throw new UnexpectedTypeException($constraint, InstallationExists::class);
        }
        foreach ($value->systemes as $systeme) {
            if (null === $value->find_installation($systeme->installation_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ installation_id }}', $systeme->installation_id)
                    ->setParameter('{{ systeme_id }}', $systeme->id)
                    ->addViolation();
            }
        }
    }
}
