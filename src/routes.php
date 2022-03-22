<?php

declare(strict_types=1);

use TechWilk\PhpTools\Controller\PhpController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;

return function (App $app) {
    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($app) {
        // Sample log message
        $this->get(LoggerInterface::class)->info("Slim-Skeleton '/' route");

        $routeParser = $app->getRouteCollector()->getRouteParser();

        // Render index view
        return $this->get(PhpRenderer::class)->render($response, 'index.phtml', [
            'classGeneratorUrl' => $routeParser->urlFor('class-generate'),
        ]);
    });

    $app->group('/php', function (Group $group) {
        $group->get('/class/generate', PhpController::class.':getClassGenerate')->setName('class-generate');
        $group->post('/class/type', PhpController::class.':postClassGenerateTypes');
        $group->post('/class/generate', PhpController::class.':postClassGenerate');
    });


    // API routes

    $app->group('/api', function (Group $group) {
        $group->group('/php', function (Group $group) {
            $group->post('/parse', PhpController::class.':postClassGenerateTypes');
            $group->post('/build', PhpController::class.':postClassGenerate');
        });

        $group->options('/{routes:.+}', function ($request, $response, $args) {
            return $response;
        });
    })->add(function (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        
        return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
};
