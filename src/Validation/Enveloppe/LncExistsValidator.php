<?php

namespace App\Validation\Enveloppe;

use App\Dto\Enveloppe\EnveloppeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LncExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof LncExists) {
            throw new UnexpectedTypeException($constraint, LncExists::class);
        }
        foreach ($value->baies as $paroi) {
            if (null === $lnc_id = $paroi->position->local_non_chauffe_id) {
                continue;
            }
            if (null === $value->find_local_non_chauffe($lnc_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ lnc_id }}', $lnc_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
        foreach ($value->murs as $paroi) {
            if (null === $lnc_id = $paroi->position->local_non_chauffe_id) {
                continue;
            }
            if (null === $value->find_local_non_chauffe($lnc_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ lnc_id }}', $lnc_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
        foreach ($value->planchers_bas as $paroi) {
            if (null === $lnc_id = $paroi->position->local_non_chauffe_id) {
                continue;
            }
            if (null === $value->find_local_non_chauffe($lnc_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ lnc_id }}', $lnc_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
        foreach ($value->planchers_hauts as $paroi) {
            if (null === $lnc_id = $paroi->position->local_non_chauffe_id) {
                continue;
            }
            if (null === $value->find_local_non_chauffe($lnc_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ lnc_id }}', $lnc_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
        foreach ($value->portes as $paroi) {
            if (null === $lnc_id = $paroi->position->local_non_chauffe_id) {
                continue;
            }
            if (null === $value->find_local_non_chauffe($lnc_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ lnc_id }}', $lnc_id)
                    ->setParameter('{{ paroi_id }}', $paroi->id)
                    ->addViolation();
            }
        }
    }
}
