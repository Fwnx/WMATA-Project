<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WmataService
{
    private string $apiKey;
    private HttpClientInterface $client;
    private WmataCacheService $cache;
    private WmataRateLimiterService $rateLimiter;
    private const BASE_URL = 'https://api.wmata.com';

    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $params,
        WmataCacheService $cache,
        WmataRateLimiterService $rateLimiter
    ) {
        $this->client = $client;
        $this->apiKey = $params->get('app.wmata_api_key');
        $this->cache = $cache;
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Get all WMATA stations
     * This data is cached for 24 hours since it should rarely change
     * @return array List of stations
     * @throws \RuntimeException if API call fails
     */
    public function getStations(): array
    {
        return $this->cache->getStations(function () {
            return $this->rateLimiter->executeWithRateLimit(function () {
                $response = $this->client->request('GET', self::BASE_URL . '/Rail.svc/json/jStations', [
                    'headers' => [
                        'api_key' => $this->apiKey,
                    ],
                ]);

                $data = $response->toArray();
                
                // Check if the response contains the expected data
                if (!isset($data['Stations'])) {
                    throw new \RuntimeException('Invalid response from WMATA API');
                }

                return $data;
            });
        });
    }

    /**
     * Get next train predictions for a station
     * This data is cached for 30 seconds to provide fresh updates while respecting rate limits
     * @param string $stationCode The station code to get predictions for
     * @return array List of predictions
     * @throws \RuntimeException if API call fails
     */
    public function getNextTrains(string $stationCode): array
    {
        return $this->cache->getPredictions($stationCode, function () use ($stationCode) {
            return $this->rateLimiter->executeWithRateLimit(function () use ($stationCode) {
                $response = $this->client->request(
                    'GET',
                    self::BASE_URL . '/StationPrediction.svc/json/GetPrediction/' . $stationCode,
                    [
                        'headers' => [
                            'api_key' => $this->apiKey,
                        ],
                    ]
                );

                $data = $response->toArray();
                
                // Check if the response contains the expected data
                if (!isset($data['Trains'])) {
                    throw new \RuntimeException('Invalid response from WMATA API');
                }

                return $data;
            });
        });
    }
} 