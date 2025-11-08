<?php

namespace App\Validation\Chauffage;

use App\Domain\Chauffage\TypeChauffage as Enum;
use App\Dto\Chauffage\ChauffageDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TypeChauffageValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof TypeChauffage) {
            throw new UnexpectedTypeException($constraint, TypeChauffage::class);
        }
        foreach ($value->systemes as $systeme) {
            foreach ($value->generateurs as $generateur) {
                if ($generateur->id !== $systeme->generateur_id) {
                    continue;
                }
                if ($systeme->type === Enum::CHAUFFAGE_CENTRAL) {
                    if ($generateur->type && false === $generateur->type->is_chauffage_central()) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ systeme_id }}', $systeme->id)
                            ->addViolation();
                    }
                }
                if ($systeme->type === Enum::CHAUFFAGE_DIVISE) {
                    if ($generateur->type && false === $generateur->type->is_chauffage_divise()) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ systeme_id }}', $systeme->id)
                            ->addViolation();
                    }
                }
            }
        }
    }
}
