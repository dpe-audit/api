<?php

namespace App\Validation\Enveloppe;

use App\Dto\Enveloppe\EnveloppeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LiaisonPontThermiqueExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof LiaisonPontThermiqueExists) {
            throw new UnexpectedTypeException($constraint, LiaisonPontThermiqueExists::class);
        }
        foreach ($value->ponts_thermiques as $pont_thermique) {
            if (null === $value->find_mur($pont_thermique->liaison->mur_id)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ pont_thermique_id }}', $pont_thermique->id)
                    ->setParameter('{{ paroi_id }}', $pont_thermique->liaison->mur_id)
                    ->addViolation();
            }
            if (null !== $paroi_id = $pont_thermique->liaison->plancher_id) {
                if (null === $value->find_plancher_bas($paroi_id) && null === $value->find_plancher_haut($paroi_id)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ pont_thermique_id }}', $pont_thermique->id)
                        ->setParameter('{{ paroi_id }}', $paroi_id)
                        ->addViolation();
                }
            }
            if (null !== $paroi_id = $pont_thermique->liaison->ouverture_id) {
                if (null === $value->find_baie($paroi_id) && null === $value->find_porte($paroi_id)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ pont_thermique_id }}', $pont_thermique->id)
                        ->setParameter('{{ paroi_id }}', $paroi_id)
                        ->addViolation();
                }
            }
        }
    }
}
