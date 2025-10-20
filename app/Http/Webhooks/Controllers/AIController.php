<?php

namespace App\Http\Webhooks\Controllers;

use App\Events\AITaskFailed;
use App\Events\AITaskSucceed;
use App\Http\Client\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ka4ivan\LaravelLogger\Facades\Llog;

final class AIController extends Controller
{
    public function handle(Request $request, string $ai, string $socketId)
    {
        Llog::info($ai, $request->all());

        if ($ai === 'novita') {
            $this->handleNovita($request, $socketId);
        }

        return 'ok';
    }

    private function handleNovita(Request $request, string $socketId)
    {
        $eventType = $request->input('event_type');

        if ($eventType !== 'ASYNC_TASK_RESULT') {
            Llog::warning('Novita: unknown event type', [$eventType]);

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        $payload = $request->input('payload');

        $task = $payload['task'];

        if (!in_array($task['task_type'], ['TXT_TO_IMG', 'IMG_TO_IMG', 'UPSCALE'])) {
            Llog::warning('Novita: unknown task type', [$task]);
        }

        if ($task['status'] !== 'TASK_STATUS_SUCCEED') {
            broadcast(new AITaskFailed($socketId, $task['task_id']));

            Llog::warning('Novita: task not succeeded', [$task]);

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        }

        foreach ($payload['images'] as $image) { // TODO
            $m = Media::fromUrl($image['image_url']);
            $m->append('links');

            $media[] = $m;

            $this->storeInCache($m);
        }

        broadcast(new AITaskSucceed($socketId, $task['task_id'], $media));

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
