<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $_ENV['CORS_ORIGIN'] ?? '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->get('/api/health', function ($request, $response) {
    $response->getBody()->write(json_encode(['status' => 'ok', 'service' => 'web']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();