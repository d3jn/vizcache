<?php

namespace D3jn\Vizcache\Exceptions;

class Handler
{
    /**
     * Get value to return for stat when specified exception occured.
     *
     * @param  \D3jn\Vizcache\Exceptions\VizcacheException $exception
     * @return mixed
     */
    public function get(VizcacheException $exception)
    {
        if (config('vizcache.silent_mode')) {
            return null;
        }

        throw $exception;
    }
}
