<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use ToysAcademy\Application\CreateArticle;
use ToysAcademy\Application\CreateCampaign;
use ToysAcademy\Application\DeleteArticle;
use ToysAcademy\Application\GetReferenceData;
use ToysAcademy\Application\GetValidatedBoxesForSubscriberByEmail;
use ToysAcademy\Application\ListArticles;
use ToysAcademy\Application\ListBoxesForCampaign;
use ToysAcademy\Application\ListCampaigns;
use ToysAcademy\Application\ListSubscribers;
use ToysAcademy\Application\Port\ArticleRepository;
use ToysAcademy\Application\Port\BoxRepository;
use ToysAcademy\Application\Port\CampaignRepository;
use ToysAcademy\Application\Port\OptimisationService;
use ToysAcademy\Application\Port\SubscriberRepository;
use ToysAcademy\Application\RunComposition;
use ToysAcademy\Application\SaveSubscriber;
use ToysAcademy\Application\UpdateArticle;
use ToysAcademy\Application\ValidateBox;
use ToysAcademy\Infrastructure\Http\ArticleController;
use ToysAcademy\Infrastructure\Http\BoxController;
use ToysAcademy\Infrastructure\Http\CampaignController;
use ToysAcademy\Infrastructure\Http\HttpOptimisationService;
use ToysAcademy\Infrastructure\Http\ReferenceController;
use ToysAcademy\Infrastructure\Http\SubscriberController;
use ToysAcademy\Infrastructure\Persistence\PdoArticleRepository;
use ToysAcademy\Infrastructure\Persistence\PdoBoxRepository;
use ToysAcademy\Infrastructure\Persistence\PdoCampaignRepository;
use ToysAcademy\Infrastructure\Persistence\PdoSubscriberRepository;

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
    $subscriberRepository = new PdoSubscriberRepository($pdo);
    $campaignRepository = new PdoCampaignRepository($pdo);
    $boxRepository = new PdoBoxRepository($pdo);

    $optimisationUrl = $_ENV['OPTIMIZATION_URL'] ?? 'http://optimisation:80';
    $optimisationService = new HttpOptimisationService($optimisationUrl);

    $listArticles = new ListArticles($articleRepository);
    $listSubscribers = new ListSubscribers($subscriberRepository);
    $createArticle = new CreateArticle($articleRepository);
    $updateArticle = new UpdateArticle($articleRepository, $boxRepository);
    $deleteArticle = new DeleteArticle($articleRepository);
    $getReferenceData = new GetReferenceData();
    $saveSubscriber = new SaveSubscriber($subscriberRepository);
    $listCampaigns = new ListCampaigns($campaignRepository);
    $createCampaign = new CreateCampaign($campaignRepository);
    $runComposition = new RunComposition($campaignRepository, $articleRepository, $subscriberRepository, $boxRepository, $optimisationService);
    $listBoxesForCampaign = new ListBoxesForCampaign($campaignRepository, $boxRepository, $articleRepository, $subscriberRepository);
    $getValidatedBoxesForSubscriberByEmail = new GetValidatedBoxesForSubscriberByEmail($subscriberRepository, $boxRepository, $articleRepository);
    $validateBox = new ValidateBox($boxRepository);

    $articleController = new ArticleController($listArticles, $articleRepository, $createArticle, $updateArticle, $deleteArticle);
    $referenceController = new ReferenceController($getReferenceData);
    $subscriberController = new SubscriberController($saveSubscriber, $listSubscribers, $subscriberRepository, $getValidatedBoxesForSubscriberByEmail);
    $campaignController = new CampaignController($listCampaigns, $createCampaign, $runComposition, $listBoxesForCampaign);
    $boxController = new BoxController($validateBox);

    $app->get('/api/reference', fn ($req, $res) => $referenceController->index($req, $res));
    $app->get('/api/articles', fn ($req, $res) => $articleController->index($req, $res));
    $app->get('/api/articles/{id}', fn ($req, $res, $args) => $articleController->show($req, $res, $args));
    $app->post('/api/admin/articles', fn ($req, $res) => $articleController->create($req, $res));
    $app->put('/api/admin/articles/{id}', fn ($req, $res, $args) => $articleController->update($req, $res, $args));
    $app->delete('/api/admin/articles/{id}', fn ($req, $res, $args) => $articleController->delete($req, $res, $args));
    $app->get('/api/subscribers', fn ($req, $res) => $subscriberController->index($req, $res));
    $app->get('/api/subscribers/by-email', fn ($req, $res) => $subscriberController->getByEmail($req, $res));
    $app->get('/api/subscribers/box', fn ($req, $res) => $subscriberController->getMyBox($req, $res));
    $app->post('/api/subscribers', fn ($req, $res) => $subscriberController->create($req, $res));
    $app->get('/api/admin/campaigns', fn ($req, $res) => $campaignController->index($req, $res));
    $app->post('/api/admin/campaigns', fn ($req, $res) => $campaignController->create($req, $res));
    $app->post('/api/admin/campaigns/{id}/compose', fn ($req, $res, $args) => $campaignController->compose($req, $res, $args));
    $app->get('/api/admin/campaigns/{id}/boxes', fn ($req, $res, $args) => $campaignController->boxes($req, $res, $args));
    $app->post('/api/admin/boxes/{id}/validate', fn ($req, $res, $args) => $boxController->validate($req, $res, $args));
}

$app->run();
