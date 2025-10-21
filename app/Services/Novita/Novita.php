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
            'filter.types' => $query['filter.types'] ?? 'lora',
            'filter.query' => $query['filter.query'] ?? null,
            'filter.is_nsfw' => false,
            'filter.is_sdxl' => $query['filter.is_sdxl'] ?? null,
            'pagination.limit' => $query['pagination.limit'] ?? 100,
            'pagination.cursor' => $query['pagination.cursor'] ?? null,
        ];

        return $this->client()
            ->get('/v3/model', $query)
            ->json() ?: [];
    }

    /**
     * @param array $request
     * @param string|null $webhookUrl
     *
     * @return string
     *
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function txt2img(array $request, ?string $webhookUrl = null): string
    {
        $data = [
            'request' => $request,
        ];

        if ($webhookUrl) {
            $data['extra']['webhook']['url'] = $webhookUrl;

            $data['extra']['webhook']['test_mode']['enabled'] = true;
            $data['extra']['webhook']['test_mode']['return_task_status'] = 'TASK_STATUS_SUCCEED';
        }

        $response = $this->client()
            ->post('/v3/async/txt2img', $data);

        $this->validateResponse($response);

        return $response->json('task_id');
    }
}
