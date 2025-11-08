<?php

namespace App\Validation\Refroidissement;

use App\Dto\Refroidissement\RefroidissementDto as Value;
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
        foreach ($value->systemes as $systeme) {
            if (null === array_find($value->generateurs, fn($item) => $item->id === $systeme->generateur_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ generateur_id }}', $systeme->generateur_id)
                    ->setParameter('{{ systeme_id }}', $systeme->id)
                    ->addViolation();
            }
        }
    }
}
