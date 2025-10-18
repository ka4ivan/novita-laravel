<?php

namespace App\Services\Novita;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Novita
{
    private const API_BASE = 'https://api.novita.ai';

    public function __construct(private readonly string $key)
    {}

    private function client(): PendingRequest
    {
        return Http::baseUrl(self::API_BASE)
            ->withToken($this->key)
            ->asJson();
    }

    /**
     * @throws NovitaException
     */
    private function validateResponse(Response $response): void
    {
        if (!$response->ok()) {
            throw new NovitaException(
                $response->json('reason', ''),
                $response->status()
            );
        }
    }

    /**
     * @param array<string, mixed> $query
     *
     * @throws ConnectionException
     */
    public function models(array $query = []): array
    {
        $query = [
            'filter.visibility' => 'public',
            'filter.types' => $query['type'] ?? 'lora',
            'filter.query' => $query['q'] ?? null,
            'filter.is_nsfw' => false,
            'filter.is_sdxl' => $query['is_sdxl'] ?? null,
            'pagination.limit' => $query['limit'] ?? 100,
            'pagination.cursor' => $query['cursor'] ?? null,
        ];

        return $this->client()
            ->get('/model', $query)
            ->json() ?: [];
    }
}
