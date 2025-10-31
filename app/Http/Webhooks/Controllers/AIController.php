<?php

namespace App\Http\Webhooks\Controllers;

use App\Events\AITaskFailed;
use App\Events\AITaskSucceed;
use App\Http\Client\Controllers\Controller;
use App\Models\AIJob;
use App\Services\Novita\NovitaDownloader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ka4ivan\LaravelLogger\Facades\Llog;

final class AIController extends Controller
{
    public function handle(Request $request, string $ai)
    {
        Llog::info($ai, $request->all());

        $aiJobId = $request->get('aiJobId');

        if ($ai === 'novita') {
            $this->handleNovita($request, $aiJobId);
        }

        return 'ok';
    }

    private function handleNovita(Request $request, string $aiJobId)
    {
        $aiJob = AIJob::find($aiJobId);
        $eventType = $request->input('event_type');

        if ($aiJob->status === AIJob::STATUS_DONE) {
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        if ($eventType !== 'ASYNC_TASK_RESULT') {
            Llog::warning('Novita: unknown event type', [$eventType]);

            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        $payload = $request->input('payload');

        $task = $payload['task'];

        if (!in_array($task['task_type'], ['TXT_TO_IMG', 'IMG_TO_IMG', 'UPSCALE'])) {
            Llog::warning('Novita: unknown task type', [$task]);

            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        if ($task['status'] !== 'TASK_STATUS_SUCCEED') {
            broadcast(new AITaskFailed($aiJobId, $task['task_id']));

            Llog::warning('Novita: task not succeeded', [$task]);

            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        foreach ($payload['images'] as $image) {
            app()->bind(NovitaDownloader::class, function () {
                return new NovitaDownloader();
            });

            config()->set('media-library.media_downloader', NovitaDownloader::class);

            $aiJob->addMediaFromUrl($image['image_url'])->toMediaCollection('image');
        }

        broadcast(new AITaskSucceed($aiJobId, $task['task_id']));

        $aiJob->update([
            'status' => AIJob::STATUS_DONE,
        ]);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
