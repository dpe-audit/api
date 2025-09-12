<?php

namespace App\Assert\Validator;

use App\Assert\ReseauFroid;
use App\Model\Refroidissement\Repository\ReseauFroidRepository;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\{UnexpectedTypeException, UnexpectedValueException};

final class ReseauFroidValidator extends ConstraintValidator
{
    public function __construct(private ReseauFroidRepository $repository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReseauFroid) {
            throw new UnexpectedTypeException($constraint, ReseauFroid::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        if (null === $this->repository->find($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
        }
    }
}
