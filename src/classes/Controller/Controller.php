<?php

namespace TechWilk\PhpTools\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Controller {

    protected $renderer;
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->setupInstanceVariables(
            $container->get(PhpRenderer::class),
            $container->get(LoggerInterface::class),
        );
    }

    protected function setupInstanceVariables(PhpRenderer $renderer, Logger $logger)
    {
        $this->renderer = $renderer;
        $this->logger = $logger;
    }

    protected $knownContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];

    /**
     * Determine which content type we know about is wanted using `Accept` header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * Slim's error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    protected function determineContentType(ServerRequestInterface $request)
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $this->knownContentTypes);

        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }

        // handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, $this->knownContentTypes)) {
                return $mediaType;
            }
        }

        return 'text/html';
    }

    protected function shouldReturnJson(ServerRequestInterface $request) 
    {
        $contentType = $this->determineContentType($request);

        return 'application/json' === $contentType;
    }
}
