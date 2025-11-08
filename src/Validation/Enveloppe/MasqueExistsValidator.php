<?php

namespace App\Validation\Enveloppe;

use App\Dto\Enveloppe\EnveloppeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class MasqueExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof MasqueExists) {
            throw new UnexpectedTypeException($constraint, MasqueExists::class);
        }
        foreach ($value->baies as $paroi) {
            foreach ($paroi->masques as $masque_id) {
                if (null === $value->find_masque($masque_id)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ masque_id }}', $masque_id)
                        ->setParameter('{{ paroi_id }}', $paroi->id)
                        ->addViolation();
                }
            }
        }
    }
}
