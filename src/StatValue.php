<?php

namespace D3jn\Vizcache;

use D3jn\Vizcache\Facades\Vizcache;

class StatValue
{
    /**
     * Name of the stat this value will be computed of.
     *
     * @var string
     */
    protected $statName;

    /**
     * Value's constructor.
     *
     * @param string $statName
     * @param array  $parameters
     */
    public function __construct(string $statName, array $parameters)
    {
        $this->statName = $statName;
        $this->parameters = $parameters;
    }
    
    /**
     * Get stat value.
     *
     * @param  mixed $default
     * @return mixed
     */
    public function get($default = null)
    {
        return Vizcache::get($this->statName, $default, $this->parameters);
    }

    /**
     * Cache stat value if it's not cached yet.
     *
     * @return void
     */
    public function touch(): void
    {
        Vizcache::touch($this->statName, $this->parameters);
    }

    /**
     * Update cached value for stat (and renew it's time to live).
     *
     * @param  mixed $default
     * @return mixed
     */
    public function update($default = null)
    {
        return Vizcache::update($this->statName, $default, $this->parameters);
    }

    /**
     * Clear stat value from cache.
     *
     * @return void
     */
    public function forget(): void
    {
        Vizcache::forget($this->statName, $this->parameters);
    }
}
