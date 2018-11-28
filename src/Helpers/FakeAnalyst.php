<?php

namespace D3jn\Vizcache\Helpers;

use D3jn\Vizcache\Facades\Vizcache;
use Illuminate\Container\Container;

class FakeAnalyst
{
    /**
     * Analyst name.
     *
     * @var string
     */
    protected $name;

    /**
     * FakeAnalyst contructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Try to return stat value by the name of unexisting method called.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return Container::getInstance()->make(
            'D3jn\Vizcache\StatValue',
            [
                'statName' => $this->name . '@' . $name,
                'parameters' => $arguments
            ]
        );
    }
}
