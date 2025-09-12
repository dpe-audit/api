<?php

namespace App\Assert\Validator;

use App\Assert\CodePostal;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\{UnexpectedTypeException, UnexpectedValueException};

final class CodePostalValidator extends ConstraintValidator
{
    public final const BASE_URL = 'https://api-adresse.data.gouv.fr/search';

    public function __construct(private HttpClientInterface $client,) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CodePostal) {
            throw new UnexpectedTypeException($constraint, CodePostal::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $response = $this->client->request('GET', self::BASE_URL, ['query' => [
            'q' => $value,
            'type' => 'municipality',
        ]]);

        if ($response->getStatusCode() !== 200) {
            return;
        }
        if (\count($response->toArray()['features'] ?? []) === 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code_postal }}', $value)
                ->addViolation();
        }
    }
}
