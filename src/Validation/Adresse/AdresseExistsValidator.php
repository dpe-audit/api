<?php

namespace App\Validation\Adresse;

use App\Dto\Adresse\AdresseDto as Value;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AdresseExistsValidator extends ConstraintValidator
{
    public final const BASE_URL = 'https://data.geopf.fr/geocodage/search';

    public function __construct(private HttpClientInterface $client,) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$constraint instanceof AdresseExists) {
            throw new UnexpectedTypeException($constraint, AdresseExists::class);
        }
        $code_postal = $value->code_postal;
        $code_insee = $value->code_insee;

        if (empty($code_postal) || empty($code_insee)) {
            return;
        }

        $response = $this->client->request('GET', self::BASE_URL, ['query' => [
            'q' => $code_postal,
            'type' => 'municipality',
            'citycode' => $code_insee,
        ]]);

        if ($response->getStatusCode() !== 200) {
            return;
        }
        if (\count($response->toArray()['features'] ?? []) === 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code_postal }}', $code_postal)
                ->setParameter('{{ code_insee }}', $code_insee)
                ->addViolation();
        }
    }
}
