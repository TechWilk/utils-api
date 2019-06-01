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


$app->get('/class/generate', PhpController::class.':getClassGenerate')->setName('class-generate');
$app->post('/class/type', PhpController::class.':postClassGenerateTypes');
$app->post('/class/generate', PhpController::class.':postClassGenerate');
