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
     * @return ClassProperty[]
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
            $scope = null;
            $type = null;

            // chop any 'protected' / 'public' (and $)
            $dollarPosition = strpos($property, '$');

            if ($dollarPosition !== false) {
                $beginning = substr($property, 0, $dollarPosition);
                $scope = $this->parseScopeFromString($beginning);

                $property = substr($property, $dollarPosition + 1);
            }

            // remove any '=' to eol
            $equalsPosition = strpos($property, '=');
            if (false !== $equalsPosition) {
                $ending = substr($property, $equalsPosition);
                $type = $this->parseDataTypeFromString($ending);
                $property = substr($property, 0, $equalsPosition);
            }
            
            $property = explode(';', $property)[0];
            $property = trim($property);

            unset($properties[$key]);
            if (empty($property)) {
                continue;
            }

            $property = new ClassProperty($property);
            $property->setScope($scope ?? 'protected');
            $property->setType($type ?? '');

            $properties[$property->getName()] = $property;
        }

        return $properties;
    }

    protected function parseScopeFromString($string)
    {
        if (strpos($string, 'private') !== false) {
            return 'private';
        }

        if (strpos($string, 'protected') !== false) {
            return 'protected';
        }

        if (strpos($string, 'public') !== false) {
            return 'public';
        }

        return null;
    }

    protected function parseDataTypeFromString($string)
    {
        // remove all whitespace
        $string = preg_replace('/\s*/m', '', $string);
      
        if (strpos($string, '=\'') === 0) {
            return 'string';
        }

        if (strpos($string, '="') === 0) {
            return 'string';
        }

        if (strpos($string, '=[') === 0) {
            return 'array';
        }

        if (strpos($string, '=false') === 0) {
            return 'bool';
        }

        if (strpos($string, '=true') === 0) {
            return 'bool';
        }

        if (preg_match('/=\d+\;$/', $string) === 1) {
            return 'int';
        }
        
        if (preg_match('/=\d+\.\d+\;$/', $string) === 1) {
            return 'float';
        }

        return null;
    }
}
