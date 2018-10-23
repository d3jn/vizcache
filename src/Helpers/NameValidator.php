<?php

namespace D3jn\Vizcache\Helpers;

class NameValidator
{
    /**
     * Check if provided analyst name is valid.
     *
     * @param  string $name
     * @return bool
     */
    public function checkAnalystName(string $name): bool
    {
        // Analyst name follows PHP class naming rules as specified here:
        //    http://php.net/manual/en/functions.user-defined.php
        return (bool) preg_match('~^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$~', $name);
    }

    /**
     * Check if provided method name is valid.
     *
     * @param  string $name
     * @return bool
     */
    public function checkMethodName(string $name): bool
    {
        // Analyst name follows PHP function naming rules as specified here:
        //    http://php.net/manual/en/language.oop5.basic.php
        return (bool) preg_match('~^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$~', $name);
    }
}
