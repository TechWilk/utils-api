<?php

namespace TechWilk\PhpTools\Php;

class Utilities
{
    /**
     * Converts camelCase string to have spaces between each.
     * 'camelCase' -> 'camel Case'
     * 
     * @param string $camelCaseString
     * @return string
     */
    function fromCamelCase($camelCaseString)
    {
        $regex = '/(?<=[a-z])(?=[A-Z])/x';
        $words = preg_split($regex, $camelCaseString);

        return implode(' ', $words);
    }

    /**
     * Converts underscore_string to camelCase.
     * 'camel_case' -> 'camelCase'
     * 
     * @param string $camelCaseString
     * @return string
     */
    function underscoreToCamelCase($underscoreString)
    {
        $words = explode('_', $underscoreString);

        $camelCaseString = '';
        foreach ($words as $word) {
            $camelCaseString .= \ucfirst($word);
        }

        return lcfirst($camelCaseString);
    }
}
