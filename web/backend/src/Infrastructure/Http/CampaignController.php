<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\CreateCampaign;
use ToysAcademy\Application\ListCampaigns;
use ToysAcademy\Domain\Campaign;

final class CampaignController
{
    public function __construct(
        private ListCampaigns $listCampaigns,
        private CreateCampaign $createCampaign,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $campaigns = ($this->listCampaigns)();
        $data = array_map(fn (Campaign $c) => [
            'id' => $c->id,
            'max_weight_per_box' => $c->maxWeightPerBox,
            'created_at' => $c->createdAt,
        ], $campaigns);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody() ?? [];
        $maxWeight = isset($body['max_weight_per_box']) ? (int) $body['max_weight_per_box'] : 0;
        try {
            $campaign = ($this->createCampaign)($maxWeight);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $data = [
            'id' => $campaign->id,
            'max_weight_per_box' => $campaign->maxWeightPerBox,
            'created_at' => $campaign->createdAt,
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
