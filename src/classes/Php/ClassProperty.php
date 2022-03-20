<?php

namespace TechWilk\PhpTools\Php;

use TechWilk\PhpTools\Php\Utilities;
use InvalidArgumentException;

class ClassProperty
{
    protected $scope;
    protected $name;
    protected $type;

    public function __construct($name = '') {
        $this->setName($name);
    }

    /**
     * @param string
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCamelCaseName()
    {
        $utilities = new Utilities();
        $name = $utilities->underscoreToCamelCase($this->name);

        return $name;
    }

    /**
     * @return string
     */
    public function getNameSentence()
    {
        $utilities = new Utilities();
        $name = $this->getCamelCaseName($this->name);
        $name = $utilities->fromCamelCase($name);

        return $name;
    }
    
    public function setType($type)
    {
        $typeLower = strtolower($type);

        switch ($typeLower) {
            case 's':
            case 'string':
                $this->type = 'string';
            break;
            case 'i':
            case 'int':
            case 'long':
            case 'integer':
                $this->type = 'int';
            break;
            case 'f':
            case 'float':
            case 'real':
            case 'double':
                $this->type = 'float';
            break;
            case 'b':
            case 'bool':
            case 'boolean':
                $this->type = 'bool';
            break;
            case 'a':
            case 'array':
                $this->type = 'array';
            break;
            case 'c':
            case 'callable':
                $this->type = 'callable';
            break;
            case 'n':
            case 'null':
                $this->type = 'null';
            break;
            case 'd':
            case 'date':
            case 'datetime':
                $this->type = 'DateTimeImmutable';
            break;
            default:
                $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
