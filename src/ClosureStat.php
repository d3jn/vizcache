<?php

namespace D3jn\Vizcache;

use Closure;
use D3jn\Vizcache\Concerns\HasStatName;

class ClosureStat
{
    use HasStatName;

    /**
     * Resolver for this stat key.
     *
     * @var \Closure|null
     */
    protected $keyResolver = null;

    /**
     * Resolver for this stat time to live.
     *
     * @var \Closure|null
     */
    protected $timeToLiveResolver = null;

    /**
     * undocumented function
     *
     * @param string $name
     * @param \Closure $resolver
     */
    public function __construct(string $name, Closure $resolver)
    {
        return null;
    }

    /**
     * Set closure for resolving this stat's key.
     *
     * @param  \Closure|null $resolver
     * @return $this
     */
    public function key(?Closure $resolver)
    {
        $this->keyResolver = $resolver;

        return $this;
    }

    /**
     * Get key resolver
     *
     * @return \Closure|null
     */
    public function getKeyResolver(): ?Closure
    {
        return $this->keyResolver;
    }

    /**
     * Set closure for resolving this stat's time to live.
     *
     * @param  \Closure|null $resolver
     * @return $this
     */
    public function timeToLive(?Closure $resolver)
    {
        $this->timeToLiveResolver = $resolver;

        return $this;
    }

    /**
     * Get time to live resolver
     *
     * @return \Closure|null
     */
    public function getTimeToLiveResolver(): ?Closure
    {
        return $this->timeToLiveResolver;
    }
}
