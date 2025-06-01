<?php

namespace App\Service;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class WmataRateLimiterService
{
    private RateLimiterFactory $secondLimiter;
    private RateLimiterFactory $dailyLimiter;
    
    public function __construct(RedisAdapter $cache)
    {
        $storage = new CacheStorage($cache);
        
        // Create rate limiter for per-second limits
        $this->secondLimiter = new RateLimiterFactory([
            'id' => 'wmata',
            'policy' => 'sliding_window',
            'limit' => 10,
            'interval' => '1 second'
        ], $storage);
        
        // Create rate limiter for daily limits
        $this->dailyLimiter = new RateLimiterFactory([
            'id' => 'wmata_daily',
            'policy' => 'fixed_window',
            'limit' => 50000,
            'interval' => '1 day'
        ], $storage);
    }

    /**
     * Attempt to consume a token from both rate limiters
     * @throws \RuntimeException if rate limit is exceeded
     */
    public function checkRateLimit(): void
    {
        $secondLimiter = $this->secondLimiter->create('wmata_api');
        $dailyLimiter = $this->dailyLimiter->create('wmata_api');
        
        // Check per-second limit
        $secondLimit = $secondLimiter->consume(1);
        if (!$secondLimit->isAccepted()) {
            $waitDuration = $secondLimit->getRetryAfter();
            throw new \RuntimeException(
                sprintf('Rate limit exceeded. Please wait %d seconds.', $waitDuration->getSeconds())
            );
        }
        
        // Check daily limit
        $dailyLimit = $dailyLimiter->consume(1);
        if (!$dailyLimit->isAccepted()) {
            throw new \RuntimeException('Daily API limit exceeded. Please try again tomorrow.');
        }
    }

    /**
     * Execute a callback with rate limiting and retry logic
     * @param callable $callback The API call to execute
     * @param int $maxRetries Maximum number of retries
     * @return mixed The result of the callback
     * @throws \RuntimeException if all retries are exhausted
     */
    public function executeWithRateLimit(callable $callback, int $maxRetries = 3): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                // Check rate limit before making the call
                $this->checkRateLimit();
                
                // Execute the API call
                return $callback();
            } catch (\RuntimeException $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $maxRetries) {
                    // Exponential backoff: 1s, 2s, 4s, etc.
                    $waitTime = pow(2, $attempt - 1);
                    sleep($waitTime);
                }
            }
        }

        throw new \RuntimeException(
            'Maximum retry attempts reached: ' . $lastException->getMessage(),
            0,
            $lastException
        );
    }
} 