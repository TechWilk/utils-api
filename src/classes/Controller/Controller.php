<?php

namespace TechWilk\PhpTools\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\PhpRenderer;

class Controller {

    protected $renderer;
    protected $logger;
    protected $router;

    public function __construct(ContainerInterface $container)
    {
        $this->setupInstanceVariables(
            $container->renderer,
            $container->logger,
            $container->router
        );
    }

    protected function setupInstanceVariables(PhpRenderer $renderer, Logger $logger, RouterInterface $router)
    {
        $this->renderer = $renderer;
        $this->logger = $logger;
        $this->router = $router;
    }
}
