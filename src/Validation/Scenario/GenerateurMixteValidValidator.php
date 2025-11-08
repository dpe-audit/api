<?php

namespace App\Validation\Scenario;

use App\Dto\Scenario\EtapeDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class GenerateurMixteValidValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof GenerateurMixteValid) {
            throw new UnexpectedTypeException($constraint, GenerateurMixteValid::class);
        }
        foreach ($value->chauffage->generateurs as $generateur) {
            if (null === $generateur->position->generateur_mixte_id) {
                continue;
            }
            if (null === $generateur_mixte = $value->ecs->find_generateur($generateur->position->generateur_mixte_id)) {
                continue;
            }
            if (null === $generateur_mixte->position->generateur_mixte_id) {
                $this->context->buildViolation($constraint::MESSAGE_GENERATEUR_MIXTE_MISSING)
                    ->setParameter('{{ generateur_mixte_id }}', $generateur->position->generateur_mixte_id)
                    ->setParameter('{{ generateur_id }}', $generateur->id)
                    ->addViolation();
            }
            if ($generateur_mixte->position->generateur_mixte_id !== $generateur->id) {
                $this->context->buildViolation($constraint::MESSAGE_GENERATEUR_MIXTE_NOT_SAME)
                    ->setParameter('{{ generateur_mixte_id }}', $generateur->position->generateur_mixte_id)
                    ->setParameter('{{ generateur_id }}', $generateur->id)
                    ->addViolation();
            }
        }
        foreach ($value->ecs->generateurs as $generateur) {
            if (null === $generateur->position->generateur_mixte_id) {
                continue;
            }
            if (null === $generateur_mixte = $value->chauffage->find_generateur($generateur->position->generateur_mixte_id)) {
                continue;
            }
            if (null === $generateur_mixte->position->generateur_mixte_id) {
                $this->context->buildViolation($constraint::MESSAGE_GENERATEUR_MIXTE_MISSING)
                    ->setParameter('{{ generateur_mixte_id }}', $generateur->position->generateur_mixte_id)
                    ->setParameter('{{ generateur_id }}', $generateur->id)
                    ->addViolation();
            }
            if ($generateur_mixte->position->generateur_mixte_id !== $generateur->id) {
                $this->context->buildViolation($constraint::MESSAGE_GENERATEUR_MIXTE_NOT_SAME)
                    ->setParameter('{{ generateur_mixte_id }}', $generateur->position->generateur_mixte_id)
                    ->setParameter('{{ generateur_id }}', $generateur->id)
                    ->addViolation();
            }
        }
    }
}
