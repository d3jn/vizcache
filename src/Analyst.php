<?php

namespace D3jn\Vizcache;

use D3jn\Vizcache\Exceptions\AnalystMethodNotFoundException;
use D3jn\Vizcache\Exceptions\Handler;

class Analyst
{
    /**
     * Manager class for determining stats cache keys/time to live.
     *
     * @var string
     */
    public $managerClass = null;

    /**
     * Manager for stats cache keys generation.
     *
     * @var mixed
     */
    protected $manager = null;

    /**
     * Handler for stats exceptions.
     *
     * @var \D3jn\Vizcache\Exceptions\Handler
     */
    protected $exceptionsHandler;

    /**
     * Analyst's constructor.
     *
     * @param \D3jn\Vizcache\Exceptions\Handler $exceptionsHandler
     * @param \D3jn\Vizcache\Manager $manager
     */
    public function __construct(Handler $exceptionsHandler, ?Manager $manager = null)
    {
        $this->exceptionsHandler = $exceptionsHandler;

        if ($this->managerClass) {
            $this->manager = app()->make($this->managerClass);
        } else {
            $this->manager = $manager;
        }
    }

    /**
     * Calculate current value of a given stat by name.
     *
     * Returns null if given stat doesn't exist or can't be calculated
     * at the moment. This implementation seeks for a class method $name in
     * current instance and uses it to calculate stat value.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return mixed|null
     * @throws \D3jn\Vizcache\Exceptions\AnalystMethodNotFoundException
     */
    public function get(string $name, array $parameters = [])
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$parameters);
        }

        $e = new AnalystMethodNotFoundException(
            "Analyst class doesn't have '$name' method!",
            $this
        );

        return $this->exceptionsHandler->get($e);
    }

    /**
     * Get hash for stat by name using it's parameters.
     *
     * Returns null if hash is not needed for this stat's cache key.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return string|null
     */
    public function hash(string $name, array $parameters = []): ?string
    {
        return $this->manager->$name(...$parameters)->getKey();
    }

    /**
     * Get hash for stat by name using it's parameters.
     *
     * Returns null if configuration value should be used.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return mixed
     */
    public function cacheStore(string $name, array $parameters = [])
    {
        return $this->manager->$name(...$parameters)->getCacheStore();
    }

    /**
     * Get time to live for stat by name using it's parameters.
     *
     * Returns null if configuration value should be used.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return mixed
     */
    public function timeToLive(string $name, array $parameters = [])
    {
        return $this->manager->$name(...$parameters)->getTimeToLive();
    }
}
