<?php

namespace App\Services\Novita;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\File\File;

class Novita
{
    private const API_BASE = 'https://api.novita.ai';

    public function __construct(private readonly string $key)
    {}

    private function client(): PendingRequest
    {
        return Http::baseUrl(self::API_BASE)
            ->withToken($this->key)
            ->timeout(300)
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

//            $data['extra']['webhook']['test_mode']['enabled'] = true;
//            $data['extra']['webhook']['test_mode']['return_task_status'] = 'TASK_STATUS_SUCCEED';
        }

        $response = $this->client()
            ->post('/v3/async/txt2img', $data);

        $this->validateResponse($response);

        return $response->json('task_id');
    }

    /**
     * @param array $request
     * @param string|null $webhookUrl
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function img2img(array $request, ?string $webhookUrl = null): string
    {
        $data = [
            'request' => $request,
        ];

        if ($webhookUrl !== null) {
            $data['extra']['webhook']['url'] = $webhookUrl;
        }

        $response = $this->client()
            ->post('/v3/async/img2img', $data);

        $this->validateResponse($response);

        return $response->json('task_id');
    }

    /**
     * @param array $request
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function qwenImageEdit(array $request): string
    {
        $response = $this->client()
            ->post('/v3/async/qwen-image-edit', $request);

        $this->validateResponse($response);

        return $response->json('task_id');
    }

    /**
     * @param array $request
     * @return array
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function gemini3ProImageEdit(array $request): array
    {
        $response = $this->client()
            ->post('v3/gemini-3-pro-image-edit', $request);

        \Llog::info($response->json());
        $this->validateResponse($response);

        return $response->json();
    }

    /**
     * @param array $request
     * @return array
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function gemini3ProImageTextToImage(array $request): array
    {
        $response = $this->client()
            ->post('v3/gemini-3-pro-image-text-to-image', $request);

        \Llog::info($response->json());
        $this->validateResponse($response);

        return $response->json();
    }

    /**
     * @param string $extension
     * @return array
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function getTrainingUploadData(string $extension): array
    {
        $response = $this->client()
            ->post('/v3/assets/training_dataset', [
                'file_extension' => $extension,
            ]);

        \Llog::info($response->body());

        $this->validateResponse($response);

        return $response->json();
    }

    /**
     * @param array $uploadData
     * @param File $file
     * @return bool
     * @throws ConnectionException
     */
    public function trainingUpload(array $uploadData, File $file): bool
    {
        $stream = Utils::streamFor(fopen($file->getRealPath(), 'r'));

        $response = Http::withBody($stream, $file->getMimeType())
            ->send($uploadData['method'], $uploadData['upload_url']);

        return $response->successful();
    }

    /**
     * @param array $request
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function trainingSubject(array $request): string
    {
        $response = $this->client()
            ->post('/v3/training/subject', $request);
        \Llog::info($response->body());

        $this->validateResponse($response);

        return $response->json('task_id');
    }

    /**
     * @param string $taskId
     * @return array
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function trainingSubjectResult(string $taskId): array
    {
        $response = $this->client()
            ->get('/training/subject', [
                'task_id' => $taskId,
            ]);

        $this->validateResponse($response);

        return $response->json();
    }

    /**
     * @param string $taskId
     * @return array
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function taskResult(string $taskId): array
    {
        $response = $this->client()
            ->get('/v3/async/task-result', [
                'task_id' => $taskId,
            ]);
        \Llog::info($response->body());
        $this->validateResponse($response);

        return $response->json();
    }

    /**
     * @param string $image
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function removeBackground(string $image): string
    {
        $response = $this->client()
            ->post('/v3/remove-background', [
                'image_file' => $image,
            ]);

        $this->validateResponse($response);

        return $response->json('image_file');
    }

    /**
     * @param string $image
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function removeText(string $image): string
    {
        $response = $this->client()
            ->post('/v3/remove-text', [
                'image_file' => $image,
            ]);

        $this->validateResponse($response);

        return $response->json('image_file');
    }

    /**
     * @param array $request
     * @param string|null $webhookUrl
     * @return string
     * @throws ConnectionException
     * @throws NovitaException
     */
    public function upscale(array $request, ?string $webhookUrl = null): string
    {
        $data = [
            'request' => $request,
        ];

        if ($webhookUrl !== null) {
            $data['extra']['webhook']['url'] = $webhookUrl;
        }

        $response = $this->client()
            ->post('/v3/async/upscale', $data);

        $this->validateResponse($response);

        return $response->json('task_id');
    }
}
