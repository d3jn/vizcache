<?php

namespace D3jn\Vizcache;

class StatStoringState
{
    /**
     * Key to use for stat in cache store.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Time to live for a stat in cache store.
     *
     * @var mixed
     */
    protected $timeToLive;

    /**
     * StatStoringState's constructor.
     *
     * @param string|null $key
     * @param mixed       $timeToLive
     */
    public function __construct(?string $key, $timeToLive = null)
    {
        $this->key = $key;
        $this->timeToLive = $timeToLive;
    }

    /**
     * Get key value.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Get time to live value.
     *
     * @return mixed
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }
}
