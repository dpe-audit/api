<?php

namespace App\Services\Observatoire;

use App\Model\Common\ValueObject\Id;
use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

final class Observatoire
{
    public final const BASE_URL = 'https://prd-x-ademe-externe-api.de-c1.eu1.cloudhub.io/api/v1/';

    private ?Error $error = null;

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {}

    private function handleError(ResponseInterface $response): void
    {
        $this->error = new Error($response->getStatusCode(), $response->getContent(false));
    }

    public function audits(Id $id): ?string
    {
        $this->error = null;
        $response = $this->client->request('GET', self::BASE_URL . "pub/audits/{$id}/xml");
        if ($response->getStatusCode() >= 300) {
            $this->handleError($response);
            return null;
        }
        return $response->getContent();
    }

    public function dpe(Id $id): ?string
    {
        $this->error = null;
        $response = $this->client->request('GET', self::BASE_URL . "pub/dpe/{$id}/xml");
        if ($response->getStatusCode() >= 300) {
            $this->handleError($response);
            return null;
        }
        return $response->getContent();
    }

    public function getError(): ?Error
    {
        return $this->error;
    }
}
