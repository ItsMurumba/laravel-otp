<?php

namespace Itsmurumba\Otp\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RateLimiter
{
    /**
     * @var string
     */
    protected $prefix = 'otp:rate-limit:';

    /**
     * @var int
     */
    protected $maxAttempts = 3;

    /**
     * @var int
     */
    protected $decayMinutes = 15;

    /**
     * Check if the identifier has exceeded the rate limit
     *
     * @param string $identifier
     * @return bool
     */
    public function tooManyAttempts(string $identifier): bool
    {
        $key = $this->getKey($identifier);
        $attempts = Cache::get($key, 0);

        return $attempts >= $this->maxAttempts;
    }

    /**
     * Increment the rate limit counter
     *
     * @param string $identifier
     * @return int
     */
    public function hit(string $identifier): int
    {
        $key = $this->getKey($identifier);
        $attempts = Cache::get($key, 0) + 1;

        Cache::put($key, $attempts, Carbon::now()->addMinutes($this->decayMinutes));

        return $attempts;
    }

    /**
     * Get the number of attempts remaining
     *
     * @param string $identifier
     * @return int
     */
    public function remaining(string $identifier): int
    {
        $key = $this->getKey($identifier);
        $attempts = Cache::get($key, 0);

        return max(0, $this->maxAttempts - $attempts);
    }

    /**
     * Reset the rate limit counter
     *
     * @param string $identifier
     * @return void
     */
    public function reset(string $identifier): void
    {
        Cache::forget($this->getKey($identifier));
    }

    /**
     * Get the cache key for the identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function getKey(string $identifier): string
    {
        return $this->prefix . $identifier;
    }

    /**
     * Set the maximum number of attempts
     *
     * @param int $maxAttempts
     * @return self
     */
    public function maxAttempts(int $maxAttempts): self
    {
        $this->maxAttempts = $maxAttempts;
        return $this;
    }

    /**
     * Set the decay time in minutes
     *
     * @param int $minutes
     * @return self
     */
    public function decayMinutes(int $minutes): self
    {
        $this->decayMinutes = $minutes;
        return $this;
    }
} 