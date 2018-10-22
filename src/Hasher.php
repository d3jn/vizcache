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
        $result = '';
        foreach ($arguments as $value) {
            $result .= $this->separator . $this->toString($value);
        }

        return $result;
    }

    /**
     * Convert value to string.
     *
     * @param  mixed $value
     * @return string
     */
    protected function toString($value): string
    {
        return (string) $value;
    }
}
