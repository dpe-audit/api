<?php

namespace App\Validation\Chauffage;

use App\Dto\Chauffage\ChauffageDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class EmetteurExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof EmetteurExists) {
            throw new UnexpectedTypeException($constraint, EmetteurExists::class);
        }
        foreach ($value->systemes as $systeme) {
            foreach ($systeme->emetteurs as $emetteur_id) {
                if (null === $value->find_emetteur($emetteur_id)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ emetteur_id }}', $emetteur_id)
                        ->setParameter('{{ systeme_id }}', $systeme->id)
                        ->addViolation();
                }
            }
        }
    }
}
