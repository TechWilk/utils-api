<?php

namespace TechWilk\PhpTools\Controller;

use TechWilk\PhpTools\Php\ClassBuilder;
use TechWilk\PhpTools\Php\ClassProperty;
use GuzzleHttp\Psr7\LazyOpenStream;

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
        $propertiesFormatted = array_map(function ($property) {
            return [
                'name' => $property->getName(),
                'scope' => $property->getScope(),
                'type' => $property->getType(),
            ];
        }, $properties);
        $propertiesFormatted = array_values($propertiesFormatted);

        $body = $response->getBody();
        $body->write(json_encode([
            'properties' => $propertiesFormatted,
        ], JSON_PRETTY_PRINT));

        return $response
            ->withStatus(200)
            ->withHeader('Content-type', 'application/json')
            ->withBody($body);
    }
    
    /**
     * Create Setters and Getters for a PHP class from a multi-line string of properties.
     */
    public function postClassGenerate($request, $response, $args)
    {
        $data = $request->getParsedBody();
        
        $propertiesGrouped = $data['properties'] ?? [];

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
            // @todo is type required? tests don't prove this - e.g. leave type empty in UI
            if (!empty($propertyArray['type'])) {
                $property->setType($propertyArray['type']);
            }

            $properties[$property->getName()] = $property;
        }

        $version = (int) $data['version'];
        $filename = 'class-generate-' . $version . '.phtml' ;

        $renderedResponse = $this->renderer->render($response, $filename, [
            'properties' => $properties,
        ]);
        $code = (string)$renderedResponse->getBody();

        $body = new LazyOpenStream('php://temp', 'r+');
        $body->write(json_encode([
            'code' => $code,
        ], JSON_PRETTY_PRINT));

        return $response
            ->withStatus(200)
            ->withHeader('Content-type', 'application/json')
            ->withBody($body);
    }
}
