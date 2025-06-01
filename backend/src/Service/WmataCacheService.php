<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\CacheException;

class WmataCacheService
{
    private CacheItemPoolInterface $cache;
    
    // Cache keys
    private const STATIONS_CACHE_KEY = 'wmata_stations';
    private const PREDICTIONS_CACHE_PREFIX = 'wmata_predictions_';
    
    // Cache durations (in seconds)
    private const STATIONS_CACHE_TTL = 86400; // 24 hours
    private const PREDICTIONS_CACHE_TTL = 30; // 30 seconds

    public function __construct(CacheItemPoolInterface $wmataCache)
    {
        $this->cache = $wmataCache;
    }

    /**
     * Get stations from cache or save them if not present
     * @param callable $fetchCallback Function to fetch stations if not in cache
     * @return array Stations data
     * @throws CacheException If cache operations fail
     */
    public function getStations(callable $fetchCallback): array
    {
        try {
            return $this->cache->get(self::STATIONS_CACHE_KEY, function (ItemInterface $item) use ($fetchCallback) {
                $item->expiresAfter(self::STATIONS_CACHE_TTL);
                return $fetchCallback();
            });
        } catch (\Exception $e) {
            throw new CacheException('Failed to get stations from cache: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get predictions from cache or save them if not present
     * @param string $stationCode The station code
     * @param callable $fetchCallback Function to fetch predictions if not in cache
     * @return array Predictions data
     * @throws CacheException If cache operations fail
     */
    public function getPredictions(string $stationCode, callable $fetchCallback): array
    {
        try {
            $cacheKey = self::PREDICTIONS_CACHE_PREFIX . $stationCode;
            
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($fetchCallback) {
                $item->expiresAfter(self::PREDICTIONS_CACHE_TTL);
                return $fetchCallback();
            });
        } catch (\Exception $e) {
            throw new CacheException('Failed to get predictions from cache: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Clear all cached predictions for a station
     * @param string $stationCode The station code
     * @throws CacheException If cache operations fail
     */
    public function clearPredictions(string $stationCode): void
    {
        try {
            $this->cache->deleteItem(self::PREDICTIONS_CACHE_PREFIX . $stationCode);
        } catch (\Exception $e) {
            throw new CacheException('Failed to clear predictions from cache: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Clear all cached stations
     * @throws CacheException If cache operations fail
     */
    public function clearStations(): void
    {
        try {
            $this->cache->deleteItem(self::STATIONS_CACHE_KEY);
        } catch (\Exception $e) {
            throw new CacheException('Failed to clear stations from cache: ' . $e->getMessage(), 0, $e);
        }
    }
} 