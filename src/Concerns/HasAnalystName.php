<?php

namespace D3jn\Vizcache\Concerns;

trait HasAnalystName
{
    /**
     * Analyst name.
     *
     * @var string
     */
    protected $analystName;

    /**
     * Get analyst name.
     *
     * @return string
     */
    public function getAnalystName(): string
    {
        return $this->analystName;
    }
}
