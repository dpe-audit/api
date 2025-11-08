<?php

namespace App\Validation\Ventilation;

use App\Dto\Ventilation\VentilationDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class GenerateurExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof GenerateurExists) {
            throw new UnexpectedTypeException($constraint, GenerateurExists::class);
        }
        foreach ($value->installations as $installation) {
            if (null === $installation->generateur_id) {
                continue;
            }
            if (null === array_find($value->generateurs, fn($item) => $item->id === $installation->generateur_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ generateur_id }}', $installation->generateur_id)
                    ->setParameter('{{ installation_id }}', $installation->id)
                    ->addViolation();
            }
        }
    }
}
