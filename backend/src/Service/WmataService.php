<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WmataService
{
    private string $apiKey;
    private HttpClientInterface $client;
    private const BASE_URL = 'https://api.wmata.com';

    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $params
    ) {
        $this->client = $client;
        $this->apiKey = $params->get('app.wmata_api_key');
    }

    public function getStations(): array
    {
        $response = $this->client->request('GET', self::BASE_URL . '/Rail.svc/json/jStations', [
            'headers' => [
                'api_key' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }

    public function getNextTrains(string $stationCode): array
    {
        $response = $this->client->request('GET', self::BASE_URL . '/StationPrediction.svc/json/GetPrediction/' . $stationCode, [
            'headers' => [
                'api_key' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }
} 