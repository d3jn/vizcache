<?php

namespace D3jn\Vizcache\Exceptions;

use D3jn\Vizcache\Concerns\HasStat;
use D3jn\Vizcache\Stat;

class StatCantBeFlushedException extends VizcacheException
{
    use HasStat;

    /**
     * StatCantBeFlushedException's constructor.
     *
     * @param string $message
     * @param \D3jn\Vizcache\Stat $stat
     */
    public function __construct(string $message, Stat $stat)
    {
        $this->stat = $stat;

        parent::__construct($message);
    }
}
