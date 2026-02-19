<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use ToysAcademy\Application\ListArticles;
use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Infrastructure\Http\ArticleController;
use ToysAcademy\Infrastructure\Persistence\PdoArticleRepository;

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

$databaseUrl = $_ENV['DATABASE_URL'] ?? '';
if ($databaseUrl !== '') {
    $parsed = parse_url($databaseUrl);
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $parsed['host'] ?? 'localhost',
        $parsed['port'] ?? 3306,
        ltrim($parsed['path'] ?? '/toys_academy', '/')
    );
    $pdo = new PDO($dsn, $parsed['user'] ?? '', $parsed['pass'] ?? '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $articleRepository = new PdoArticleRepository($pdo);
    $listArticles = new ListArticles($articleRepository);
    $articleController = new ArticleController($listArticles);
    $app->get('/api/articles', fn ($req, $res) => $articleController->index($req, $res));
}

$app->run();
