<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ToysAcademy\Application\GetReferenceData;

final class ReferenceController
{
    public function __construct(
        private GetReferenceData $getReferenceData,
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = ($this->getReferenceData)();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
