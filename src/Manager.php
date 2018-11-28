<?php

namespace D3jn\Vizcache;

use BadMethodCallException;
use LogicException;

class Manager
{
    /**
     * Whether to do auto hashing for stats that doesn't have respective hash
     * methods.
     *
     * @var bool
     */
    protected $autoHashing = true;

    /**
     * List of stat names to use auto hashing on if their respective hash methods
     * are not implemented. If $autoHashing is set true this value changes nothing.
     *
     * @var array
     */
    protected $autoHashStats = [];

    /**
     * Hasher instance.
     *
     * @var mixed
     */
    protected $hasher;

    /**
     * Manager's constructor.
     *
     * @param \D3jn\Vizcache\Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Handle unexisting configuration resolution logic of manager class.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $result = preg_match('~^(?<stat>.+)_(?<type>hash|ttl|store)$~', $name, $matches);

        if (! $result) {
            throw new BadMethodCallException(sprintf(
                'Method %s() doesn\'t exist nor can it be resolved by %s!',
                $name,
                static::class
            ));
        }

        // For hash resolution we additionally check if auto hashing was provided
        // for stat and use it if so.
        if ($matches['type'] == 'hash') {
            if ($this->autoHashing || in_array($matches['stat'], $this->autoHashStats)) {
                return $this->hasher->hash($arguments);
            }
        }

        // By default we return null, meaning that no special configuration
        // resolution logic exists.
        return null;
    }

    /**
     * Set auto hashing option.
     *
     * @param  bool $autoHashing
     * @return void
     */
    public function setAutoHashing(bool $autoHashing): void
    {
        $this->autoHashing = $autoHashing;
    }

    /**
     * Set auto hashing option.
     *
     * @param  array $autoHashStats
     * @return void
     */
    public function setAutoHashStats(array $autoHashStats): void
    {
        $this->autoHashStats = $autoHashStats;
    }
}
