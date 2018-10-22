<?php

namespace D3jn\Vizcache\Concerns;

use D3jn\Vizcache\Stat;

trait HasStat
{
    /**
     * Stat instance.
     *
     * @var \D3jn\Vizcache\Stat
     */
    protected $stat;

    /**
     * Get stat instance.
     *
     * @return \D3jn\Vizcache\Stat
     */
    public function getStat(): Stat
    {
        return $this->stat;
    }
}
