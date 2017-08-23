<?php

namespace TechWilk\PhpTools\Controller;

use TechWilk\PhpTools\Php\ClassBuilder;
use TechWilk\PhpTools\Php\ClassProperty;

class PhpController extends Controller
{
    public function getClassGenerate($request, $response, $args)
    {
        return $this->renderer->render($response, 'class-generate-form.phtml');
    }

    public function postClassGenerateTypes($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        $classBuilder = new ClassBuilder;
        $properties = $classBuilder->parsePropertiesStringIntoArray($data['properties']);

        return $this->renderer->render($response, 'class-generate-type-form.phtml', [
            'properties' => $properties,
        ]);
    }
    
    /**
     * Create Setters and Getters for a PHP class from a multi-line string of properties.
     * 
     * @param [type] $request
     * @param [type] $response
     * @param array $args
     * @return void
     */
    public function postClassGenerate($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        $propertiesGrouped = [];
        foreach ($data['property'] as $name => $value) {
            $name = explode('/', $name);

            $propertiesGrouped[$name[0]][$name[1]] = $value;
        }

        $properties = [];
        foreach ($propertiesGrouped as $propertyArray) {
            if (empty($propertyArray['enabled'])) {
                
                continue;
            }

            $property = new ClassProperty();
            
            if (!empty($propertyArray['property'])) {
                $property->setName($propertyArray['property']);
            }
            if (!empty($propertyArray['scope'])) {
                $property->setScope($propertyArray['scope']);
            }
            if (!empty($propertyArray['type'])) {
                $property->setType($propertyArray['type']);
            }

            $properties[$property->getName()] = $property;
        }

        $version = (int) $data['version'];
        $filename = 'class-generate-' . $version . '.phtml' ;

        return $this->renderer->render($response, $filename, [
            'properties' => $properties,
        ]);
    }
}
