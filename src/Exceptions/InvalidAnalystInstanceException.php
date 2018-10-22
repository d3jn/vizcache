<?php

namespace D3jn\Vizcache\Exceptions;

use D3jn\Vizcache\Concerns\HasAnalystName;
use D3jn\Vizcache\Stat;

class InvalidAnalystInstanceException extends VizcacheException
{
    use HasAnalystName;

    /**
     * AnalystNotFoundException's constructor.
     *
     * @param string $message
     * @param string $analystName
    */
    public function __construct(string $message, string $analystName)
    {
        $this->analystName = $analystName;

        parent::__construct($message);
    }
}
