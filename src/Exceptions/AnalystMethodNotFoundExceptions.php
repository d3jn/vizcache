<?php

namespace D3jn\Vizcache\Exceptions;

use D3jn\Vizcache\Analyst;
use D3jn\Vizcache\Concerns\HasAnalyst;

class AnalystMethodNotFoundException extends VizcacheException
{
    use HasAnalyst;

    /**
     * AnalystNotFoundException's constructor.
     *
     * @param string $message
     * @param \D3jn\Vizcache\Analyst $analyst
     */
    public function __construct(string $message, Analyst $analyst)
    {
        $this->analyst = $analyst;

        parent::__construct($message);
    }
}
