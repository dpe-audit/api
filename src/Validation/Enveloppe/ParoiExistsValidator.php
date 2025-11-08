<?php

namespace App\Validation\Enveloppe;

use App\Dto\Enveloppe\EnveloppeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ParoiExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof ParoiExists) {
            throw new UnexpectedTypeException($constraint, ParoiExists::class);
        }
        foreach ($value->baies as $paroi) {
            if (null === $paroi_id = $paroi->position->paroi_id) {
                continue;
            }
            $reference = $value->find_mur($paroi_id)
                ?? $value->find_plancher_bas($paroi_id)
                ?? $value->find_plancher_haut($paroi_id);

            if (null === $reference) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', $paroi->id)
                    ->setParameter('{{ paroi_id }}', $paroi_id)
                    ->addViolation();
            }
        }
        foreach ($value->portes as $paroi) {
            if (null === $paroi_id = $paroi->position->paroi_id) {
                continue;
            }
            $reference = $value->find_mur($paroi_id)
                ?? $value->find_plancher_bas($paroi_id)
                ?? $value->find_plancher_haut($paroi_id);

            if (null === $reference) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', $paroi->id)
                    ->setParameter('{{ paroi_id }}', $paroi_id)
                    ->addViolation();
            }
        }
    }
}
