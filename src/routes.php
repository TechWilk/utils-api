<?php
// Routes

use TechWilk\PhpTools\Controller\PhpController;

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', [
        'classGeneratorUrl' => $this->router->pathFor('class-generate'),
    ]);
});

$app->group('/php', function ($app) {
    $this->get('/class/generate', PhpController::class.':getClassGenerate')->setName('class-generate');
    $this->post('/class/type', PhpController::class.':postClassGenerateTypes');
    $this->post('/class/generate', PhpController::class.':postClassGenerate');
});


// API routes

$app->group('/api', function ($app) {
    $this->group('/php', function ($app) {
        $this->post('/parse', PhpController::class.':postClassGenerateTypes');
        $this->post('/build', PhpController::class.':postClassGenerate');
    });

    $this->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });
})->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
