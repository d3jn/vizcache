<?php

namespace D3jn\Vizcache\Concerns;

use D3jn\Vizcache\Stat;

trait HasStatName
{
    /**
     * Stat name.
     *
     * @var string
     */
    protected $statName;

    /**
     * Get stat name.
     *
     * @return string
     */
    public function getStatName(): string
    {
        return $this->statName;
    }
}
