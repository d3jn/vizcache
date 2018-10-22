<?php

namespace D3jn\Vizcache\Concerns;

use D3jn\Vizcache\Exceptions\Handler;

trait HasExceptionsHandler
{
    /**
     * Exceptions handler instance.
     *
     * @var \D3jn\Vizcache\Exceptions\Handler
     */
    protected $exceptionsHandler;

    /**
     * Get exceptions handler instance.
     *
     * @return \D3jn\Vizcache\Exceptions\Handler
     */
    public function getExceptionsHandler(): string
    {
        return $this->exceptionsHandler;
    }
}
