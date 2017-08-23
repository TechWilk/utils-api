<?php

namespace TechWilk\PhpTools\Php;

use TechWilk\PhpTools\Php\ClassProperty;

class ClassBuilder {
    
    /**
     * Parse a multi-line string of PHP class properties.
     * 
     * Removes any scope keywords (private/public), blank lines, leading space, trailing space, '$', ';'
     *
     * @param string $multilineString
     * @return array[ClassProperty]
     */
    public function parsePropertiesStringIntoArray($multilineString)
    {
        // remove comments
        $multilineString = preg_replace('/\/\*.+?\*\//sm', '', $multilineString);
        $multilineString = preg_replace('/^\s*\/\/.+/m', '', $multilineString);

        $properties = explode(PHP_EOL, $multilineString);
        
        // remove empty lines
        $properties = array_filter($properties, function ($property) {
            return !empty(trim($property));
        });

        foreach ($properties as $key => $property) {

            // chop any 'protected' / 'public' (and $)
            $dollarPosition = strpos($property, '$');

            if ($dollarPosition !== false) {
                $property = substr($property, $dollarPosition + 1);
            }

            // remove any '=' to eol
            $equalsPosition = strpos($property, '=');
            if (false !== $equalsPosition) {
                $property = substr($property, 0, $equalsPosition);
            }
            
            $property = explode(';', $property)[0];
            $property = trim($property);

            $property = new ClassProperty($property);
            $property->setScope('protected');

            unset($properties[$key]);
            $properties[$property->getName()] = $property;
        }

        return $properties;
    }
}
