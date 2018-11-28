<?php

namespace D3jn\Vizcache;

use Closure;
use D3jn\Vizcache\Analyst;
use D3jn\Vizcache\Exceptions\AnalystNotFoundException;
use D3jn\Vizcache\Exceptions\InvalidAnalystInstanceException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class Vizcache
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application $app
     */
    protected $app;

    /**
     * Stats constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get value for stat with specified name.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @param  array      $parameters
     * @return mixed
     */
    public function get(string $name, $default = null, array $parameters = [])
    {
        $value = $this->resolveStat($name, $default, $parameters)->value();

        return ($value !== null) ? $value : $default;
    }

    /**
     * Get value for stat with specified name directly from analyst. Any existing
     * cache configuration for it is ignored.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @param  array      $parameters
     * @return mixed
     */
    public function compute(string $name, $default = null, array $parameters = [])
    {
        $value = $this->resolveStat($name, $default, $parameters)->compute();

        return ($value !== null) ? $value : $default;
    }

    /**
     * Delete cached value of specified stat if it's exists.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @param  array      $parameters
     * @return void
     */
    public function forget(string $name, array $parameters = [])
    {
        $this->resolveStat($name, null, $parameters)->forget();
    }

    /**
     * Delete all cached values of specified stat if cache store supports it.
     *
     * @param  string $name
     * @return void
     */
    public function flush(string $name)
    {
        $this->resolveStat($name, null, [])->flush();
    }

    /**
     * Cache stat value if it's not cached yet.
     *
     * @param  string $name
     * @param  array  $parameters
     * @return void
     */
    public function touch(string $name, array $parameters = []): void
    {
        $this->resolveStat($name, null, $parameters)->touch();
    }

    /**
     * Update stat value in the cache based on it's configuration.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @param  array      $parameters
     * @return void
     */
    public function update(string $name, $default = null, array $parameters = [])
    {
        $this->resolveStat($name, $default, $parameters)->update();
    }

    /**
     * Return fake analyst by the name of unexisting method called.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return Container::getInstance()->make('D3jn\Vizcache\Helpers\FakeAnalyst', compact('name'));
    }

    /**
     * Get stat object for given name request.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @param  array      $parameters
     * @return \D3jn\Vizcache\Stat
     */
    protected function resolveStat(string $name, $default = null, array $parameters = []): Stat
    {
        $extracted = $this->extractAnalystAndMethod($name);
        if (empty($extracted)) {
            throw new InvalidStatNameException(sprintf('Can\'t parse stat name "%s"!', $name), $name);
        }

        list($analystName, $methodName) = $extracted;

        $analyst = $this->resolveAnalyst($analystName);
        if ($analyst === null) {
            return $default;
        }

        $configuration = $this->getConfigurationForStat($analystName, $methodName);

        // If analyst resolves cache store we use it's value over configuration one.
        $cacheStore = $analyst->cacheStore($methodName, $parameters);
        if ($cacheStore !== null) {
            $configuration['cache_store'] = $cacheStore;
        }

        return Container::getInstance()->make(
            'D3jn\Vizcache\Stat',
            compact('analyst', 'configuration', 'analystName', 'methodName', 'parameters')
        );
    }

    /**
     * Get associative array of configuration for stat by name.
     *
     * Available settings: 'cache_store', 'time_to_live'.
     *
     * @param  string $analystName
     * @param  string $methodName
     * @return array
     */
    protected function getConfigurationForStat(string $analystName, string $methodName): array
    {
        // Starting with default configuration.
        $result = $this->getDefaultConfigurationForStat();

        $stat = "$analystName.$methodName";
        foreach ($this->getConfiguration() as $mask => $configuration) {
            if (Str::is($mask, $stat)) {
                $result = array_merge($result, $configuration);
            }
        }

        return $result;
    }

    /**
     * Get configuration map ordered by key length.
     *
     * @return array
     */
    protected function getConfiguration(): array
    {
        $configuration = config('vizcache.configuration');

        // Sorting configuration map by key length.
        $keys = array_map('strlen', array_keys($configuration));
        array_multisort($keys, SORT_ASC, $configuration);

        return $configuration;
    }

    /**
     * Get array of default configuration for stats.
     *
     * @return array
     */
    protected function getDefaultConfigurationForStat(): array
    {
        return [
            'cache_store' => false,
            'time_to_live' => 60,
            'only_get_from_cache' => false
        ];
    }

    /**
     * Resolve analyst based on provided stat name.
     *
     * @param  string $name
     * @return \D3jn\Vizcache\Analyst|null
     */
    protected function resolveAnalyst(string $name): ?Analyst
    {
        $class = config("vizcache.analysts.{$name}");

        if (! ($class && class_exists($class))) {
            throw new AnalystNotFoundException(sprintf(
                'Can\'t find analyst class %s for "%s"! Check \'analysts\' configuration key in <config/vizcache.php> file!',
                $class,
                $name
            ), $name);
        }

        $analyst = Container::getInstance()->make($class);
        if (! $analyst instanceof Analyst) {
            throw new InvalidAnalystInstanceException(sprintf(
                '"%s" analyst class must be instance of D3jn\Vizcache\Analyst!',
                $name
            ), $name);
        }

        return $analyst;
    }

    /**
     * Get analyst and method names from query stat string and return those
     * as array.
     *
     * Returns null if name is invalid.
     *
     * @param  string $name
     * @return ?array
     */
    protected function extractAnalystAndMethod(string $name): ?array
    {
        // Trying to match by "@" or "." separator.
        if (preg_match('/^(?<analyst>[^@\.]+)[@\.](?<method>[^@\.]+)$/', $name, $matches)) {
            return [$matches['analyst'], $matches['method']];
        }

        return null;
    }
}
