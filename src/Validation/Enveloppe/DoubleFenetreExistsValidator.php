<?php

namespace App\Validation\Enveloppe;

use App\Dto\Enveloppe\EnveloppeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DoubleFenetreExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof DoubleFenetreExists) {
            throw new UnexpectedTypeException($constraint, DoubleFenetreExists::class);
        }
        foreach ($value->baies as $paroi) {
            if (null === $double_fenetre_id = $paroi->position->double_fenetre_id) {
                continue;
            }
            if (null === $value->find_double_fenetre($double_fenetre_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ double_fenetre_id }}', $double_fenetre_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
    }
}
