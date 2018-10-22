<?php

namespace D3jn\Vizcache\Facades;

use Illuminate\Support\Facades\Facade;

class Vizcache extends Facade
{
    /**
     * Get facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vizcache';
    }
}
