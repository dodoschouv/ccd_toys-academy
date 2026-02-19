<?php

declare(strict_types=1);

namespace ToysAcademy\Infrastructure\Http;

use ToysAcademy\Application\Port\OptimisationService;

final class HttpOptimisationService implements OptimisationService
{
    public function __construct(
        private string $baseUrl,
    ) {
    }

    public function compute(string $csvInput): string
    {
        $url = rtrim($this->baseUrl, '/') . '/api/compute';
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('Impossible d\'initialiser cURL');
        }
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $csvInput,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/plain; charset=utf-8',
            ],
            CURLOPT_TIMEOUT => 60,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err !== '') {
            throw new \RuntimeException('Erreur optimisation (curl): ' . $err);
        }
        if ($response === false) {
            throw new \RuntimeException('Réponse optimisation vide');
        }
        if ($httpCode !== 200) {
            throw new \RuntimeException('Erreur optimisation HTTP ' . $httpCode . ': ' . substr((string) $response, 0, 500));
        }
        return (string) $response;
    }
}
