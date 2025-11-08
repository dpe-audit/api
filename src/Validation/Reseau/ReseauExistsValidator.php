<?php

namespace App\Validation\Reseau;

use App\Domain\Reseau\ReseauRepository;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\{UnexpectedTypeException, UnexpectedValueException};

final class ReseauExistsValidator extends ConstraintValidator
{
    public function __construct(private ReseauRepository $repository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReseauExists) {
            throw new UnexpectedTypeException($constraint, ReseauExists::class);
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
