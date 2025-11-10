<?php

namespace App\Validation\Schema;

use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Opis\JsonSchema\{Helper, Validator as JsonSchemaValidator};
use Opis\JsonSchema\Errors\ErrorFormatter;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Yaml\Yaml;

final class SchemaValidator extends ConstraintValidator
{
    public function __construct(private ContainerBagInterface $params) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Schema) {
            throw new UnexpectedTypeException($constraint, Schema::class);
        }
        if (false === is_object($value)) {
            throw new UnexpectedTypeException($constraint, 'object');
        }
        if (false === method_exists($value, '__normalize')) {
            throw new UnexpectedTypeException($constraint, 'object with method __normalize');
        }
        if (null === $path = $this->params->get($constraint->schemaId)) {
            throw new UnexpectedTypeException($constraint, "schema {$constraint->schemaId} not found");
        }
        $schema = json_encode(Yaml::parseFile($path));
        $data = Helper::toJson($value->__normalize());
        $validator = new JsonSchemaValidator();
        $validation = $validator->validate($data, $schema);

        if ($validation->isValid()) {
            return;
        }
        $errors = (new ErrorFormatter())->format($validation->error());
        foreach ($errors as $path => $pathErrors) {
            foreach ($pathErrors as $message) {
                $this->context->buildViolation("{$path}: {$message}")->addViolation();
            }
        }
    }
}
