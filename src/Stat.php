<?php

namespace D3jn\Vizcache;

use Closure;
use D3jn\Vizcache\Concerns\HasAnalystName;
use D3jn\Vizcache\Exceptions\StatCantBeFlushedException;
use Illuminate\Cache\TaggableStore;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Stat
{
    use HasAnalystName;

    /**
     * Analyst of this stat.
     *
     * @var \D3jn\Vizcache\Analyst
     */
    public $analyst;

    /**
     * Cache configuration.
     *
     * @var array
     */
    public $configuration;

    /**
     * Stat analyst method name.
     *
     * @var string
     */
    public $methodName;

    /**
     * Parameters for stat analyst method.
     *
     * @var array
     */
    public $parameters;

    /**
     * Stat constructor.
     *
     * @param \D3jn\Vizcache\Analyst $analyst
     * @param array  $configuration
     * @param string $analystName
     * @param string $methodName
     * @param array  $parameters
     */
    public function __construct(
        Analyst $analyst,
        array $configuration,
        string $analystName,
        string $methodName,
        array $parameters
    ) {
        $this->analyst = $analyst;
        $this->configuration = $configuration;
        $this->analystName = $analystName;
        $this->methodName = $methodName;
        $this->parameters = $parameters;
    }

    /**
     * Return stat value.
     *
     * @return mixed
     */
    public function value()
    {
        if ($this->getCacheStore() && $this->isCachingAllowed()) {
            return $this->getCachedValue();
        }

        return $this->compute();
    }

    /**
     * Return newly computed stat value. Any existing cache configuration for
     * it is ignored.
     *
     * @return mixed
     */
    public function compute()
    {
        return $this->analyst->get($this->methodName, $this->parameters);
    }

    /**
     * Forget stat value if cached.
     *
     * @return mixed
     */
    public function forget()
    {
        $this->resolveCacheRepository()->forget($this->getNameToStore());
    }

    /**
     * Delete all cached values of this stat if cache store supports it.
     *
     * @return void
     */
    public function flush()
    {
        $repository = $this->resolveCacheRepository();
        if (! $repository->getStore() instanceof TaggableStore) {
            throw new StatCantBeFlushedException(
                'Can\'t flush stat because it\'s configured cache store doesn\'t support tagging!',
                $this
            );
        }

        $repository->tags($this->getTags())->flush();
    }

    /**
     * Cache stat value if it's not cached yet.
     *
     * @return mixed
     */
    public function touch()
    {
        if ($this->getCacheStore() && $this->isCachingAllowed()) {
            $repository = $this->resolveCacheRepository();
            $keyName = $this->getNameToStore();

            if (! $repository->has($keyName)) {
                $this->resolveCacheRepository()->put(
                    $this->getNameToStore(),
                    $this->analyst->get($this->methodName, $this->parameters),
                    $this->getTimeToLive()
                );
            }
        }
    }

    /**
     * Force update stat value in cache and refresh it's ttl.
     *
     * If stat is not configured to be cached then does nothing.
     *
     * @return void
     */
    public function update(): void
    {
        if (! $this->getCacheStore()) {
            return;
        }

        $repository = $this->resolveCacheRepository();
        if (! $repository) {
            return;
        }

        $keyName = $this->getNameToStore();

        $this->putValue(
            $repository,
            $this->getTags(),
            $keyName,
            $this->getTimeToLive(),
            $this->analyst->get($this->methodName, $this->parameters)
        );
    }

    /**
     * Get this stat name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->analystName . '@' . $this->methodName;
    }

    /**
     * Get cache store name to use for this stat.
     *
     * @return string
     */
    protected function getCacheStore(): string
    {
        return $this->analyst->cacheStore($this->methodName, $this->parameters)
            ?: $this->configuration['cache_store'];
    }

    /**
     * Resolve cache repository instance with proper store for this stat.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function resolveCacheRepository(): Repository
    {
        return Cache::store($this->getCacheStore());
    }

    /**
     * Return stat value from cache or calculate it and store to cache.
     *
     * @return mixed
     */
    protected function getCachedValue()
    {
        $keyName = $this->getNameToStore();
        $repository = Cache::store($this->getCacheStore());

        if ($this->configuration['only_get_from_cache']) {
            if ($repository->has($keyName)) {
                return $repository->get($keyName);
            }

            // Null will mean that default value should be returned.
            return null;
        }

        return $this->rememberValue(
            $repository,
            $this->getTags(),
            $keyName,
            $this->getTimeToLive(),
            function () {
                return $this->analyst->get($this->methodName, $this->parameters);
            }
        );
    }

    /**
     * Get array of tags for current stat.
     *
     * @return array
     */
    protected function getTags(): array
    {
        return [$this->analystName, $this->methodName];
    }

    /**
     * Cache value to repository by specified key name and return it.
     *
     * If time to live is false then value is stored forever.
     *
     * @param  \Illuminate\Contracts\Cache\Repository $repository
     * @param  array    $tags
     * @param  string   $keyName
     * @param  int|bool $timeToLive
     * @param  \Closure $closure
     * @return mixed
     */
    protected function rememberValue(Repository $repository, array $tags, string $keyName, $timeToLive, Closure $closure)
    {
        if ($repository->getStore() instanceof TaggableStore) {
            $repository = $repository->tags($tags);
        }

        if ($timeToLive == false) {
            return $repository->rememberForever($keyName, $closure);
        }

        return $repository->remember($keyName, $timeToLive, $closure);
    }

    /**
     * Force cache value to repository by specified key name.
     *
     * If time to live is negative then value is stored forever.
     *
     * @param  \Illuminate\Contracts\Cache\Repository $repository
     * @param  array  $tags
     * @param  string $keyName
     * @param  mixed  $timeToLive
     * @param  mixed  $value
     * @return void
     */
    protected function putValue(Repository $repository, array $tags, string $keyName, $timeToLive, $value): void
    {
        if ($repository->getStore() instanceof TaggableStore) {
            $repository = $repository->tags($tags);
        }

        if ($timeToLive == false) {
            $repository->forever($keyName, $value);
        }

        $repository->put($keyName, $value, $timeToLive);
    }

    /**
     * Get name to use for caching.
     *
     * @return string
     */
    protected function getNameToStore(): string
    {
        $base = "_{$this->analystName}_{$this->methodName}";
        $hash = $this->analyst->hash($this->methodName, $this->parameters);

        // Null-value identical check used because empty string is considered
        // to be valid hash value.
        return ($hash !== null)
            ? $base . '@' . $hash
            : $base;
    }

    /**
     * Get time to live to use for caching.
     *
     * @return mixed
     */
    protected function getTimeToLive()
    {
        return $this->analyst->timeToLive($this->methodName, $this->parameters)
            ?: $this->configuration['time_to_live'];
    }

    /**
     * Return true if caching is allowed regardless of stat context and false otherwise.
     *
     * @return bool
     */
    protected function isCachingAllowed(): bool
    {
        // If caching is disabled for testing environment then we allow it
        // for every environment other than 'testing'.
        if (config('vizcache.no_caching_when_testing', false)) {
            return ! Container::getInstance()->environment('testing');
        }

        return true;
    }
}
