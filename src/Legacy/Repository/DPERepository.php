<?php

namespace App\Legacy\Repository;

use App\Legacy\Model\DPE;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DPERepository
{
    public final const BASE_URL = 'https://prd-x-ademe-externe-api.de-c1.eu1.cloudhub.io/api/v1/';

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {}

    public function find(string $id): ?DPE
    {
        $response = $this->client->request('GET', self::BASE_URL . "pub/dpe/{$id}/xml");
        if ($response->getStatusCode() !== 200) {
            return null;
        }
        $xml = simplexml_load_string($response->getContent());
        return DPE::from($xml);
    }
}
