<?php

namespace D3jn\Vizcache;

use D3jn\Vizcache\Exceptions\AnalystMethodNotFoundException;
use Illuminate\Support\Facades\App;

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
     * Memoized values of already resolved configurations.
     *
     * @var array
     */
    protected $memoized = [];

    /**
     * Analyst's constructor.
     *
     * @param \D3jn\Vizcache\Manager $manager
     */
    public function __construct(?Manager $manager = null)
    {
        if ($this->managerClass) {
            $this->manager = App::make($this->managerClass);
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
     */
    public function get(string $name, array $parameters = [])
    {
        if (! method_exists($this, $name)) {
            throw new AnalystMethodNotFoundException(
                sprintf("Analyst class doesn't have '%s' method!", $name),
                $this
            );
        }

        return $this->$name(...$parameters);
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
        return $this->memoize('hash', function () use ($name, $parameters) {
            return optional($this->manager)->{$name . '_hash'}(...$parameters);
        });
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
        return $this->memoize('cacheStore', function () use ($name, $parameters) {
            return optional($this->manager)->{$name . '_store'}(...$parameters);
        });
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
        return $this->memoize('timeToLive', function () use ($name, $parameters) {
            return optional($this->manager)->{$name . '_ttl'}(...$parameters);
        });
    }

    /**
     * Get memoized value.
     *
     * @param  string   $name
     * @param  \Closure $value
     * @return mixed
     */
    protected function memoize($name, \Closure $value)
    {
        if (! isset($this->memoized[$name])) {
            $this->memoized[$name] = $value();
        }

        return $this->memoized[$name];
    }
}
