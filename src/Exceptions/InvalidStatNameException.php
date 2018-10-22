<?php

namespace D3jn\Vizcache\Exceptions;

use D3jn\Vizcache\Concerns\HasStatName;

class InvalidStatNameException extends VizcacheException
{
    use HasStatName;

    /**
     * InvalidStatNameException's constructor.
     *
     * @param string $message
     * @param string $statName
     */
    public function __construct(string $message, string $statName)
    {
        $this->statName = $statName;

        parent::__construct($message);
    }
}
