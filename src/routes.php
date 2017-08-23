<?php
// Routes

use TechWilk\PhpTools\Controller\PhpController;

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


$app->get('/class/generate', PhpController::class.':getClassGenerate');
$app->post('/class/type', PhpController::class.':postClassGenerateTypes');
$app->post('/class/generate', PhpController::class.':postClassGenerate');
