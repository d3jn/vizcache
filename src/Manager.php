<?php

namespace D3jn\Vizcache;

class Manager
{
    /**
     * Whether to do auto hashing for unexisting methods.
     *
     * @var bool
     */
    protected $autoHashing = true;

    /**
     * List of unexisting method names to use auto hashing on. If $autoHashing is
     * set true this value affects nothing.
     *
     * @var array
     */
    protected $auto = [];

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
     * Return null-value when unexisting method is called.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return \D3jn\Vizcache\StatStoringState
     */
    public function __call(string $name, array $arguments): StatStoringState
    {
        if ($this->autoHashing || in_array($name, $this->auto)) {
            return $this->state(
                $this->hasher->hash($arguments),
                null
            );
        }

        return $this->state(null, null);
    }

    /**
     * Create stat state instance based on provided parameters.
     *
     * @param  string|null $key
     * @param  mixed       $timeToLive
     * @return \D3jn\Vizcache\StatStoringState
     */
    protected function state(?string $key, $timeToLive = null): StatStoringState
    {
        return app()->make('D3jn\Vizcache\StatStoringState', [
            'key' => $key,
            'timeToLive' => $timeToLive
        ]);
    }
}
