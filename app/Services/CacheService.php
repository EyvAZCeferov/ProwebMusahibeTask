<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Closure;

class CacheService
{
    protected CacheRepository $cache;

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function getOrSet(string $key, Closure $callback)
    {
        return $this->cache->rememberForever($key, $callback);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return void
     */
    public function forget(string $key): void
    {
        $this->cache->forget($key);
    }
}
