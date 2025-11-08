<?php

namespace App\Validation\Chauffage;

use App\Dto\Chauffage\ChauffageDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CascadeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof Cascade) {
            throw new UnexpectedTypeException($constraint, Cascade::class);
        }
        foreach ($value->systemes as $systeme) {
            $id = $systeme->id;
            $generateur_id = $systeme->generateur_id;
            $cascade = $systeme->cascade;

            foreach ($value->systemes as $compare) {
                if ($id === $compare->id) {
                    continue;
                }
                if ($generateur_id !== $compare->generateur_id) {
                    continue;
                }
                if ($cascade !== $compare->cascade) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ id }}', $generateur_id)
                        ->addViolation();
                }
            }
        }
    }
}
