<?php

namespace D3jn\Vizcache;

class Hasher
{
    /**
     * Arguments separator.
     *
     * @var string
     */
    protected $separator;

    /**
     * Hasher's constructor.
     *
     * @param string $separator
     */
    public function __construct(string $separator = '_')
    {
        $this->separator = $separator;
    }

    /**
     * Hash array of arguments into string.
     *
     * @param  array $arguments
     * @return string
     */
    public function hash(array $arguments): string
    {
        return $this->separator . md5(serialize($arguments));
    }
}
