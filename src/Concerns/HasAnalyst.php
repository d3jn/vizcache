<?php

namespace D3jn\Vizcache\Concerns;

use D3jn\Vizcache\Analyst;

trait HasAnalyst
{
    /**
     * Analyst instance.
     *
     * @var string
     */
    protected $analyst;

    /**
     * Get analyst name in question.
     *
     * @return \D3jn\Vizcache\Analyst
     */
    public function getAnalyst(): Analyst
    {
        return $this->analyst;
    }
}
