<?php

namespace App\Assert\Validator;

use App\Assert\Ventilation as Assert;
use App\Model\Ressource;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class VentilationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Ressource) {
            throw new UnexpectedTypeException($constraint, Ressource::class);
        }
        if (!$constraint instanceof Assert) {
            throw new UnexpectedTypeException($constraint, Assert::class);
        }

        $this->validate_generateurs($value);
        $this->validate_systemes($value);
    }

    private function validate_generateurs(Ressource $value): void
    {
        foreach ($value->ventilation()->generateurs() as $item) {
            if ($item->annee_installation()?->less_than($value->batiment()->annee_construction)) {
                $this->context
                    ->buildViolation(Assert::ANNEE_INSTALLATION_INVALID)
                    ->setParameter('%x%', $item->annee_installation())
                    ->setParameter('%g%', $item->id())
                    ->addViolation();
            }
        }
    }

    private function validate_systemes(Ressource $value): void
    {
        foreach ($value->ventilation()->systemes() as $item) {
            if (null === $value->ventilation()->installations()->find($item->installation_id())) {
                $this->context
                    ->buildViolation(Assert::INSTALLATION_NOT_FOUND)
                    ->setParameter('%i%', $item->installation_id())
                    ->setParameter('%s%', $item->id())
                    ->addViolation();
            }
            if ($item->generateur_id() && !$value->ventilation()->generateurs()->find($item->generateur_id())) {
                $this->context
                    ->buildViolation(Assert::GENERATEUR_NOT_FOUND)
                    ->setParameter('%g%', $item->generateur_id())
                    ->setParameter('%s%', $item->id())
                    ->addViolation();
            }
        }
    }
}
